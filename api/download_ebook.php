<?php
/**
 * M2B E-book Secure Download API
 * Serves personalized watermarked PDFs to verified buyers
 * 
 * Endpoint: GET /api/download_ebook.php?order_id=XXX&token=XXX
 */

require_once '../config.php';

// Validate required parameters
$orderId = $_GET['order_id'] ?? '';
$token = $_GET['token'] ?? '';

if (!$orderId || !$token) {
    http_response_code(400);
    echo '<!DOCTYPE html><html><body><h2>Link tidak valid</h2><p>Parameter tidak lengkap.</p></body></html>';
    exit;
}

// Verify token (HMAC signature with optional expiration)
$expectedToken = hash_hmac('sha256', $orderId, API_SECRET_KEY);
if (!hash_equals($expectedToken, $token)) {
    http_response_code(403);
    echo '<!DOCTYPE html><html><body><h2>Akses ditolak</h2><p>Token tidak valid.</p></body></html>';
    exit;
}

// Rate limiting: max 10 downloads per IP per hour
$dlRateFile = __DIR__ . '/../temp/dl_' . md5($_SERVER['REMOTE_ADDR']) . '.json';
if (file_exists($dlRateFile)) {
    $dlRate = json_decode(file_get_contents($dlRateFile), true);
    if ($dlRate && $dlRate['count'] >= 10 && (time() - $dlRate['first']) < 3600) {
        http_response_code(429);
        echo '<!DOCTYPE html><html><body><h2>Terlalu banyak permintaan</h2><p>Silakan coba lagi nanti.</p></body></html>';
        exit;
    }
    if (time() - $dlRate['first'] >= 3600) {
        $dlRate = ['count' => 0, 'first' => time()];
    }
} else {
    $dlRate = ['count' => 0, 'first' => time()];
}
$dlRate['count']++;
@file_put_contents($dlRateFile, json_encode($dlRate));

// Connect to database and verify order
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]);
} catch (PDOException $e) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><body><h2>Server Error</h2><p>Silakan coba lagi nanti.</p></body></html>';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND payment_status = 'verified'");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    http_response_code(404);
    echo '<!DOCTYPE html><html><body><h2>Pesanan tidak ditemukan</h2><p>Order belum terverifikasi atau tidak valid.</p></body></html>';
    exit;
}

// Look for watermarked PDF
$safeOrderId = preg_replace('/[^A-Za-z0-9_-]/', '', $orderId);
$pdfPath = __DIR__ . '/../temp/ebook-' . $safeOrderId . '.pdf';

if (!file_exists($pdfPath)) {
    // Fallback: redirect to Google Drive
    header('Location: ' . GOOGLE_DRIVE_LINK);
    exit;
}

// Serve the watermarked PDF
$filename = 'M2B-Ebook-' . $safeOrderId . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($pdfPath));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

readfile($pdfPath);
exit;
