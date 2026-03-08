<?php
/**
 * M2B E-book Payment Verification API
 * Admin approves/rejects payment and triggers email delivery
 * 
 * Endpoint: POST /api/verify_payment.php
 * Headers: Authorization: Bearer <API_SECRET_KEY>
 * Content-Type: application/json
 * 
 * Request Body:
 * {
 *   "order_id": "M2B-20260106-XXXXXX",
 *   "action": "approve" | "reject"
 * }
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://ebook.m2b.co.id');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config.php';

// Composer autoload for FPDI
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

// Check if helpers exist
$emailHelperPath = __DIR__ . '/../email_helper.php';
$watermarkPath = __DIR__ . '/../watermark_pdf.php';

if (file_exists($emailHelperPath)) {
    require_once $emailHelperPath;
}
if (file_exists($watermarkPath)) {
    require_once $watermarkPath;
}

// Check API authorization
$headers = getallheaders();
$apiKey = '';

// Try different header formats
if (isset($headers['Authorization'])) {
    $apiKey = str_replace('Bearer ', '', $headers['Authorization']);
} elseif (isset($headers['authorization'])) {
    $apiKey = str_replace('Bearer ', '', $headers['authorization']);
} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $apiKey = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
}

// Validate API key
if (empty($apiKey) || $apiKey !== API_SECRET_KEY) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized - Invalid API key'
    ]);
    exit;
}

// Database connection
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

// Handle POST request only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Get request data
$input = json_decode(file_get_contents('php://input'), true);

$orderId = $input['order_id'] ?? '';
$action = $input['action'] ?? '';

// Validate parameters
if (empty($orderId) || empty($action)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters: order_id and action'
    ]);
    exit;
}

if (!in_array($action, ['approve', 'reject'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action. Must be "approve" or "reject"'
    ]);
    exit;
}

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Order not found'
    ]);
    exit;
}

// Check if already processed
if ($order['payment_status'] !== 'pending') {
    http_response_code(409);
    echo json_encode([
        'success' => false,
        'message' => 'Order already processed',
        'current_status' => $order['payment_status']
    ]);
    exit;
}

try {
    if ($action === 'approve') {
        // Process approval
        $ebookUrl = GOOGLE_DRIVE_LINK;
        $watermarkInfo = null;
        $emailSent = false;
        $watermarkSuccess = false;
        
        // Generate personalized watermarked PDF if enabled
        if (WATERMARK_ENABLED && class_exists('PDFWatermark')) {
            try {
                $watermarker = new PDFWatermark();
                // Pick correct master PDF based on buyer's language choice
                $lang = $order['ebook_lang'] ?? 'id';
                $masterPdf = ($lang === 'en' && defined('MASTER_EBOOK_PATH_EN')) 
                    ? MASTER_EBOOK_PATH_EN 
                    : MASTER_EBOOK_PATH;
                // Safe filename: sanitize orderId
                $safeOrderId = preg_replace('/[^A-Za-z0-9_-]/', '', $orderId);
                $outputPdf = __DIR__ . '/../temp/ebook-' . $safeOrderId . '.pdf';
                
                $watermarkInfo = $watermarker->addWatermark(
                    $masterPdf,
                    $outputPdf,
                    $order['buyer_name'],
                    $order['buyer_email']
                );
                
                // If FPDI watermark succeeded, create a per-buyer download link
                if ($watermarkInfo && isset($watermarkInfo['success']) && $watermarkInfo['success'] && $watermarkInfo['method'] === 'fpdi') {
                    $watermarkSuccess = true;
                    $ebookUrl = SITE_URL . '/api/download_ebook.php?order_id=' . urlencode($orderId) . '&token=' . urlencode(hash_hmac('sha256', $orderId, API_SECRET_KEY));
                }
            } catch (Exception $e) {
                error_log('Watermark error: ' . $e->getMessage());
                $watermarkInfo = ['error' => $e->getMessage()];
            }
        }
        
        // Send delivery email if helper exists (BEFORE updating DB so we know the result)
        if (class_exists('EmailHelper')) {
            try {
                $emailHelper = new EmailHelper();
                $order['ebook_url'] = $ebookUrl;
                $emailSent = $emailHelper->sendEbookDelivery($order);
            } catch (Exception $e) {
                error_log('Email delivery error: ' . $e->getMessage());
            }
        }
        
        // Update order status with correct email_sent value
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET payment_status = 'verified',
                verified_at = NOW(),
                watermark_status = ?,
                email_sent = ?,
                ebook_url = ?
            WHERE order_id = ?
        ");
        $stmt->execute([
            $watermarkSuccess ? 'completed' : 'pending',
            $emailSent ? 1 : 0, 
            $ebookUrl, 
            $orderId
        ]);
        
        // Generate WhatsApp notification URL for buyer
        $waMessage = "✅ *PEMBAYARAN TERVERIFIKASI*\n\n";
        $waMessage .= "Halo {$order['buyer_name']},\n\n";
        $waMessage .= "Terima kasih! Pembayaran Anda untuk:\n";
        $waMessage .= "📋 Order ID: *{$orderId}*\n\n";
        $waMessage .= "telah kami verifikasi.\n\n";
        $waMessage .= "📥 *Download E-book:*\n{$ebookUrl}\n\n";
        $waMessage .= "E-book telah di-watermark khusus untuk Anda:\n";
        $waMessage .= "👤 {$order['buyer_name']}\n";
        $waMessage .= "📧 {$order['buyer_email']}\n\n";
        $waMessage .= "Jika ada pertanyaan, hubungi kami via:\n";
        $waMessage .= "📧 " . SUPPORT_EMAIL . "\n";
        $waMessage .= "💬 " . TELEGRAM_GROUP . "\n\n";
        $waMessage .= "Selamat belajar! 📚🚀";
        
        // Clean buyer's WhatsApp number
        $buyerWa = preg_replace('/[^0-9]/', '', $order['buyer_whatsapp']);
        $whatsappUrl = "https://wa.me/{$buyerWa}?text=" . urlencode($waMessage);
        
        // Log approval
        error_log("Order approved: {$orderId} - Email sent: " . ($emailSent ? 'Yes' : 'No'));
        
        echo json_encode([
            'success' => true,
            'message' => 'Payment approved successfully',
            'order_id' => $orderId,
            'email_sent' => $emailSent,
            'ebook_url' => $ebookUrl,
            'whatsapp_url' => $whatsappUrl,
            'watermark_info' => $watermarkInfo ? [
                'success' => $watermarkInfo['success'] ?? false,
                'method' => $watermarkInfo['method'] ?? 'unknown',
                'page_count' => $watermarkInfo['page_count'] ?? null,
                'message' => $watermarkInfo['message'] ?? ''
            ] : null
        ]);
        
    } elseif ($action === 'reject') {
        // Process rejection
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET payment_status = 'failed',
                verified_at = NOW()
            WHERE order_id = ?
        ");
        $stmt->execute([$orderId]);
        
        // Send rejection email
        $rejectionEmailSent = false;
        if (class_exists('EmailHelper')) {
            try {
                $emailHelper = new EmailHelper();
                $rejectionEmailSent = $emailHelper->sendPaymentRejection($order);
            } catch (Exception $e) {
                error_log('Rejection email error: ' . $e->getMessage());
            }
        }
        
        // Log rejection
        error_log("Order rejected: {$orderId} - Email sent: " . ($rejectionEmailSent ? 'Yes' : 'No'));
        
        echo json_encode([
            'success' => true,
            'message' => 'Payment rejected',
            'order_id' => $orderId,
            'rejection_email_sent' => $rejectionEmailSent
        ]);
    }
    
} catch (PDOException $e) {
    error_log('Verification error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to process verification'
    ]);
}
