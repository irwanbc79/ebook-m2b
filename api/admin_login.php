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
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config.php';

// Hardcoded admin credentials (override config.php if needed)
// Change these values and redeploy to update admin password
$ADMIN_USER = 'admin';
$ADMIN_PASS = 'admin123';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username dan password wajib diisi']);
    exit;
}

// Validate credentials
if ($username === $ADMIN_USER && $password === $ADMIN_PASS) {
    echo json_encode([
        'success' => true,
        'message' => 'Login berhasil',
        'api_key' => defined('API_SECRET_KEY') ? API_SECRET_KEY : 'm2b_default_key',
        'admin_whatsapp' => defined('ADMIN_WHATSAPP') ? ADMIN_WHATSAPP : '6282261846811',
        'expires_in' => 86400
    ]);
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Username atau password salah']);
}
