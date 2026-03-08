<?php
/**
 * M2B Admin Login API
 * Validates admin credentials and returns API key for subsequent requests
 * 
 * POST /api/admin_login.php
 * Body: { "username": "admin", "password": "xxx" }
 * Returns: { "success": true, "api_key": "xxx", "expires_in": 86400 }
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://ebook.m2b.co.id');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Brute-force rate limiting (max 5 attempts per IP per 15 minutes)
$loginRateFile = __DIR__ . '/../temp/login_' . md5($_SERVER['REMOTE_ADDR']) . '.json';
if (file_exists($loginRateFile)) {
    $rateData = json_decode(file_get_contents($loginRateFile), true);
    if ($rateData && $rateData['count'] >= 5 && (time() - $rateData['first']) < 900) {
        http_response_code(429);
        echo json_encode(['success' => false, 'message' => 'Terlalu banyak percobaan login. Coba lagi dalam 15 menit.']);
        exit;
    }
    if (time() - $rateData['first'] >= 900) {
        $rateData = ['count' => 0, 'first' => time()];
    }
} else {
    $rateData = ['count' => 0, 'first' => time()];
}

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username dan password wajib diisi']);
    exit;
}

// Validate credentials with timing-safe comparison
if (hash_equals('admin', $username) && hash_equals(ADMIN_PASSWORD, $password)) {
    // Reset rate limit on success
    @unlink($loginRateFile);

    echo json_encode([
        'success' => true,
        'message' => 'Login berhasil',
        'api_key' => API_SECRET_KEY,
        'admin_whatsapp' => ADMIN_WHATSAPP,
        'expires_in' => 86400
    ]);
} else {
    // Increment failed attempts
    $rateData['count']++;
    @file_put_contents($loginRateFile, json_encode($rateData));

    error_log('Failed admin login attempt from ' . $_SERVER['REMOTE_ADDR']);
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Username atau password salah']);
}
