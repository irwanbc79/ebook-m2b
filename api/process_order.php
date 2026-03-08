<?php
/**
 * M2B E-book Order Processing API
 * Handles new order submission from landing page
 * 
 * Endpoint: POST /api/process_order.php
 * Content-Type: application/json
 * 
 * Request Body:
 * {
 *   "name": "Nama Pembeli",
 *   "email": "email@example.com",
 *   "whatsapp": "08xxxxxxxxxx",
 *   "city": "Kota",
 *   "purpose": "Tujuan pembelian (optional)"
 * }
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://ebook.m2b.co.id');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config.php';

// Simple rate limiting (max 5 orders per IP per hour)
$rateLimitFile = __DIR__ . '/../temp/rate_' . md5($_SERVER['REMOTE_ADDR']) . '.json';
if (file_exists($rateLimitFile)) {
    $rateData = json_decode(file_get_contents($rateLimitFile), true);
    if ($rateData && $rateData['count'] >= 5 && (time() - $rateData['first']) < 3600) {
        http_response_code(429);
        echo json_encode(['success' => false, 'message' => 'Terlalu banyak permintaan. Coba lagi nanti.']);
        exit;
    }
    if (time() - $rateData['first'] >= 3600) {
        $rateData = ['count' => 0, 'first' => time()];
    }
} else {
    $rateData = ['count' => 0, 'first' => time()];
}
$rateData['count']++;
@file_put_contents($rateLimitFile, json_encode($rateData));

// Check if email_helper exists
$emailHelperPath = __DIR__ . '/../email_helper.php';
$emailHelperExists = file_exists($emailHelperPath);
if ($emailHelperExists) {
    require_once $emailHelperPath;
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
    error_log('Database connection error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

// Create orders table if not exists
$createTable = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) UNIQUE NOT NULL,
    buyer_name VARCHAR(255) NOT NULL,
    buyer_email VARCHAR(255) NOT NULL,
    buyer_whatsapp VARCHAR(50) NOT NULL,
    buyer_city VARCHAR(100) NOT NULL,
    buyer_purpose TEXT,
    payment_status ENUM('pending', 'verified', 'failed') DEFAULT 'pending',
    ebook_url TEXT,
    watermark_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    ebook_lang VARCHAR(5) DEFAULT 'id',
    email_sent TINYINT(1) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verified_at TIMESTAMP NULL,
    INDEX idx_order_id (order_id),
    INDEX idx_payment_status (payment_status),
    INDEX idx_email (buyer_email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $pdo->exec($createTable);
    
    // Add missing columns if table was created before schema update
    $alterQueries = [
        "ALTER TABLE orders ADD COLUMN IF NOT EXISTS email_sent TINYINT(1) DEFAULT 0",
        "ALTER TABLE orders ADD COLUMN IF NOT EXISTS watermark_status ENUM('pending','completed','failed') DEFAULT 'pending'",
        "ALTER TABLE orders ADD COLUMN IF NOT EXISTS ebook_lang VARCHAR(5) DEFAULT 'id'",
        "ALTER TABLE orders ADD COLUMN IF NOT EXISTS ebook_url TEXT",
        "ALTER TABLE orders ADD COLUMN IF NOT EXISTS notes TEXT",
        "ALTER TABLE orders ADD COLUMN IF NOT EXISTS verified_at TIMESTAMP NULL"
    ];
    foreach ($alterQueries as $q) {
        try { $pdo->exec($q); } catch (PDOException $ignore) {}
    }
} catch (PDOException $e) {
    error_log('Table creation error: ' . $e->getMessage());
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

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Fallback to $_POST if JSON decode fails
if (!$data) {
    $data = $_POST;
}

// Validate required fields
$required = ['name', 'email', 'whatsapp', 'city'];
$errors = [];

foreach ($required as $field) {
    if (empty($data[$field])) {
        $errors[] = "Field '{$field}' is required";
    }
}

// Validate email format
if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

// Return validation errors
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $errors
    ]);
    exit;
}

// Generate unique Order ID
$orderId = 'M2B-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

// Clean WhatsApp number
$whatsapp = preg_replace('/[^0-9]/', '', $data['whatsapp']);
if (substr($whatsapp, 0, 2) === '08') {
    $whatsapp = '62' . substr($whatsapp, 1);
} elseif (substr($whatsapp, 0, 2) !== '62') {
    $whatsapp = '62' . $whatsapp;
}

// Prepare order data
$orderData = [
    'order_id' => $orderId,
    'buyer_name' => trim($data['name']),
    'buyer_email' => trim(strtolower($data['email'])),
    'buyer_whatsapp' => $whatsapp,
    'buyer_city' => trim($data['city']),
    'buyer_purpose' => isset($data['purpose']) ? trim($data['purpose']) : '',
    'ebook_lang' => isset($data['ebook_lang']) && in_array($data['ebook_lang'], ['id', 'en']) ? $data['ebook_lang'] : 'id'
];

try {
    // Insert into database
    $stmt = $pdo->prepare("
        INSERT INTO orders (order_id, buyer_name, buyer_email, buyer_whatsapp, buyer_city, buyer_purpose, ebook_lang)
        VALUES (:order_id, :buyer_name, :buyer_email, :buyer_whatsapp, :buyer_city, :buyer_purpose, :ebook_lang)
    ");
    
    $stmt->execute($orderData);
    
    // Send confirmation email if helper exists
    $emailSent = false;
    if ($emailHelperExists && class_exists('EmailHelper')) {
        try {
            $emailHelper = new EmailHelper();
            $emailSent = $emailHelper->sendOrderConfirmation($orderData);
            
            // Update email_sent status
            if ($emailSent) {
                $pdo->prepare("UPDATE orders SET email_sent = 1 WHERE order_id = ?")->execute([$orderId]);
            }
        } catch (Exception $e) {
            error_log('Email sending error: ' . $e->getMessage());
        }
    }
    
    // Generate WhatsApp URL
    $whatsappUrl = generateWhatsAppURL($orderData);
    
    // Log successful order
    error_log("New order created: {$orderId} - {$orderData['buyer_name']} ({$orderData['buyer_email']})");
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Order berhasil dibuat!',
        'order_id' => $orderId,
        'email_sent' => $emailSent,
        'whatsapp_url' => $whatsappUrl,
        'data' => [
            'name' => $orderData['buyer_name'],
            'email' => $orderData['buyer_email'],
            'price' => 'Rp ' . number_format(EBOOK_PRICE, 0, ',', '.'),
            'bank' => BANK_NAME,
            'account' => BANK_ACCOUNT,
            'holder' => BANK_HOLDER
        ]
    ]);
    
} catch (PDOException $e) {
    error_log('Order creation error: ' . $e->getMessage());
    
    // Check for duplicate order
    if ($e->getCode() == 23000) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Order already exists'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create order',
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Generate WhatsApp confirmation URL
 */
function generateWhatsAppURL($orderData) {
    $phone = ADMIN_WHATSAPP;
    
    $message = "🎉 *PESANAN E-BOOK M2B*\n\n";
    $message .= "📋 *Order ID:* {$orderData['order_id']}\n\n";
    $message .= "👤 *Data Pembeli:*\n";
    $message .= "• Nama: {$orderData['buyer_name']}\n";
    $message .= "• Email: {$orderData['buyer_email']}\n";
    $message .= "• WhatsApp: {$orderData['buyer_whatsapp']}\n";
    $message .= "• Kota: {$orderData['buyer_city']}\n\n";
    $message .= "💳 *Informasi Pembayaran:*\n";
    $message .= "• Bank: " . BANK_NAME . "\n";
    $message .= "• No. Rek: " . BANK_ACCOUNT . "\n";
    $message .= "• A.N.: " . BANK_HOLDER . "\n";
    $message .= "• Jumlah: Rp " . number_format(EBOOK_PRICE, 0, ',', '.') . "\n\n";
    $message .= "📌 *Langkah Selanjutnya:*\n";
    $message .= "1. Transfer sejumlah Rp " . number_format(EBOOK_PRICE, 0, ',', '.') . "\n";
    $message .= "2. Kirim bukti transfer + Order ID ini\n";
    $message .= "3. E-book dikirim maks 2 jam setelah verifikasi\n\n";
    $message .= "Terima kasih! 🙏";
    
    return "https://wa.me/{$phone}?text=" . urlencode($message);
}
