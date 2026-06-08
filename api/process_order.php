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

// Dynamic CORS with whitelist
$allowedOrigins = [
    'https://ebook.m2b.co.id',
    'https://m2b.co.id',
    'http://localhost',
    'http://127.0.0.1',
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin && in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
} elseif ($origin && preg_match('/^https:\/\/.*\.m2b\.co\.id$/', $origin)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
} else {
    header('Access-Control-Allow-Origin: https://ebook.m2b.co.id');
}
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config.php';

// Better logging helper
function logOrder($message) {
    $logDir = __DIR__ . '/../temp';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $logFile = $logDir . '/orders.log';
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

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
    
    // Get existing columns to avoid syntax errors on older MySQL versions
    $existingColumns = [];
    try {
        $columnsStmt = $pdo->query("DESCRIBE orders");
        while ($col = $columnsStmt->fetch(PDO::FETCH_ASSOC)) {
            $existingColumns[] = strtolower($col['Field']);
        }
    } catch (PDOException $e) {
        error_log('Describe table error: ' . $e->getMessage());
    }

    // Add missing columns safely
    $alterQueries = [
        'email_sent' => "ALTER TABLE orders ADD COLUMN email_sent TINYINT(1) DEFAULT 0",
        'watermark_status' => "ALTER TABLE orders ADD COLUMN watermark_status ENUM('pending','completed','failed') DEFAULT 'pending'",
        'ebook_lang' => "ALTER TABLE orders ADD COLUMN ebook_lang VARCHAR(5) DEFAULT 'id'",
        'ebook_url' => "ALTER TABLE orders ADD COLUMN ebook_url TEXT",
        'notes' => "ALTER TABLE orders ADD COLUMN notes TEXT",
        'verified_at' => "ALTER TABLE orders ADD COLUMN verified_at TIMESTAMP NULL",
        'payment_proof' => "ALTER TABLE orders ADD COLUMN payment_proof VARCHAR(255) DEFAULT NULL"
    ];
    foreach ($alterQueries as $colName => $q) {
        if (!in_array(strtolower($colName), $existingColumns, true)) {
            try { 
                $pdo->exec($q); 
            } catch (PDOException $ignore) {}
        }
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
$orderId = 'M2B-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

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
    
    // Google Sheets webhook integration (optional)
    if (defined('GOOGLE_SHEETS_WEBHOOK_URL') && GOOGLE_SHEETS_WEBHOOK_URL) {
        try {
            $webhookPayload = json_encode([
                'order_id' => $orderId,
                'buyer_name' => $orderData['buyer_name'],
                'buyer_email' => $orderData['buyer_email'],
                'buyer_whatsapp' => $orderData['buyer_whatsapp'],
                'buyer_city' => $orderData['buyer_city'],
                'buyer_purpose' => $orderData['buyer_purpose'],
                'ebook_lang' => $orderData['ebook_lang'],
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $webhookOptions = [
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
                    'content' => $webhookPayload,
                    'timeout' => 5
                ]
            ];
            $webhookContext = stream_context_create($webhookOptions);
            @file_get_contents(GOOGLE_SHEETS_WEBHOOK_URL, false, $webhookContext);
            logOrder("Webhook sent for order {$orderId}");
        } catch (Exception $e) {
            logOrder("Webhook failed for order {$orderId}: " . $e->getMessage());
        }
    } else {
        logOrder("Webhook skipped for order {$orderId}: GOOGLE_SHEETS_WEBHOOK_URL not configured");
    }
    
    // Log successful order
    logOrder("New order created: {$orderId} - {$orderData['buyer_name']} ({$orderData['buyer_email']})");
    
    $adminPanelUrl = defined('ADMIN_PANEL_URL') ? ADMIN_PANEL_URL : 'https://ebook.m2b.co.id/admin.html';
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Order berhasil dibuat!',
        'order_id' => $orderId,
        'email_sent' => $emailSent,
        'whatsapp_url' => $whatsappUrl,
        'admin_notify_url' => $adminPanelUrl . '?highlight=' . urlencode($orderId),
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
