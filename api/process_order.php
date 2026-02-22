<?php
/**
 * M2B E-book - Process Order API
 * Receives order from frontend form, saves to DB, returns order ID & WhatsApp URL
 * 
 * POST /api/process_order.php
 * Body: { name, email, whatsapp, city, purpose }
 * Response: { success, order_id, whatsapp_url }
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Load config
$configPath = __DIR__ . '/../config.php';
if (!file_exists($configPath)) {
    // Fallback: return minimal success so frontend can handle it client-side
    $orderId = generateOrderId();
    $waUrl = buildWhatsAppUrl($orderId, [], '6282261846811');
    echo json_encode([
        'success' => true,
        'order_id' => $orderId,
        'whatsapp_url' => $waUrl,
        'note' => 'config.php not found, order saved client-side only'
    ]);
    exit;
}

require_once $configPath;

// Read input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

// Validate required fields
$name     = trim($input['name'] ?? '');
$email    = trim($input['email'] ?? '');
$whatsapp = trim($input['whatsapp'] ?? '');
$city     = trim($input['city'] ?? '');
$purpose  = trim($input['purpose'] ?? '');

if (empty($name) || empty($email) || empty($whatsapp) || empty($city)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nama, email, WhatsApp, dan kota wajib diisi']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid']);
    exit;
}

// Generate Order ID
$orderId = generateOrderId();

// Price
$price = defined('EBOOK_PRICE') ? EBOOK_PRICE : 49000;

// WhatsApp number
$waNumber = defined('ADMIN_WHATSAPP') ? ADMIN_WHATSAPP : '6282261846811';

// Purpose label mapping
$purposeLabels = [
    'bisnis'      => 'Memulai Bisnis Ekspor/Impor',
    'umkm'        => 'Scale Up UMKM',
    'belajar'     => 'Belajar / Riset',
    'profesional' => 'Pengembangan Karir',
    'lainnya'     => 'Lainnya',
];
$purposeLabel = $purposeLabels[$purpose] ?? 'Tidak dipilih';

// Try to save to database
$dbSaved = false;
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );

    $stmt = $pdo->prepare("
        INSERT INTO orders (order_id, buyer_name, buyer_email, buyer_whatsapp, buyer_city, purchase_purpose, amount, payment_status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    $stmt->execute([$orderId, $name, $email, $whatsapp, $city, $purposeLabel, $price]);
    $dbSaved = true;
} catch (PDOException $e) {
    // Log error but don't fail — frontend has localStorage fallback
    error_log("M2B Order DB Error: " . $e->getMessage());
}

// Send confirmation email (non-blocking, don't fail if email fails)
try {
    $emailHelperPath = __DIR__ . '/email_helper.php';
    if ($dbSaved && file_exists($emailHelperPath)) {
        require_once $emailHelperPath;
        $emailHelper = new EmailHelper();
        $emailHelper->sendOrderConfirmation([
            'order_id'       => $orderId,
            'buyer_name'     => $name,
            'buyer_email'    => $email,
            'buyer_whatsapp' => $whatsapp,
            'buyer_city'     => $city,
            'purchase_purpose' => $purposeLabel,
            'amount'         => $price,
        ]);
    }
} catch (Exception $e) {
    error_log("M2B Email Error: " . $e->getMessage());
}

// Build WhatsApp URL
$orderData = [
    'name'    => $name,
    'email'   => $email,
    'whatsapp' => $whatsapp,
    'city'    => $city,
    'purpose' => $purposeLabel,
];
$waUrl = buildWhatsAppUrl($orderId, $orderData, $waNumber);

// Return success
echo json_encode([
    'success'      => true,
    'order_id'     => $orderId,
    'whatsapp_url' => $waUrl,
    'db_saved'     => $dbSaved,
]);

// ── Helper Functions ──

function generateOrderId() {
    $date = date('Ymd');
    $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    return "M2B-{$date}-{$random}";
}

function buildWhatsAppUrl($orderId, $data, $waNumber) {
    $priceFormatted = 'Rp 49.000';
    
    $name    = $data['name'] ?? '';
    $email   = $data['email'] ?? '';
    $wa      = $data['whatsapp'] ?? '';
    $city    = $data['city'] ?? '';
    $purpose = $data['purpose'] ?? 'Tidak dipilih';

    $message = "Halo M2B, saya ingin memesan E-book Ekspor Impor v2.0\n\n"
             . "📋 *Detail Pesanan*\n"
             . "Order ID: {$orderId}\n"
             . "Nama: {$name}\n"
             . "Email: {$email}\n"
             . "WhatsApp: {$wa}\n"
             . "Kota: {$city}\n"
             . "Tujuan: {$purpose}\n\n"
             . "Total: {$priceFormatted}\n\n"
             . "Saya akan segera melakukan pembayaran. Terima kasih! 🙏";

    return "https://wa.me/{$waNumber}?text=" . rawurlencode($message);
}
