<?php
/**
 * M2B Admin — Ganti Password
 *
 * GET  /api/admin_change_password.php  → info profil (kapan terakhir diubah)
 * POST /api/admin_change_password.php  → ganti password
 * Body: { "current_password": "...", "new_password": "..." }
 *
 * Headers: Authorization: Bearer <session token dari admin_login.php>
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://ebook.m2b.co.id');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config.php';
require_once '../admin_auth_store.php';

// ── Auth: token sesi yang sama dengan endpoint admin lain ──
$headers = function_exists('getallheaders') ? getallheaders() : [];
$apiKey = '';
if (isset($headers['Authorization'])) {
    $apiKey = str_replace('Bearer ', '', $headers['Authorization']);
} elseif (isset($headers['authorization'])) {
    $apiKey = str_replace('Bearer ', '', $headers['authorization']);
} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $apiKey = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
}

$isAuthorized = false;
if (!empty($apiKey)) {
    if (hash_equals(API_SECRET_KEY, $apiKey)) {
        $isAuthorized = true;
    } else {
        $parts = explode('|', $apiKey, 2);
        if (count($parts) === 2) {
            $tokenHash = $parts[0];
            $expiry = (int) $parts[1];
            if (time() <= $expiry) {
                $expected = hash_hmac('sha256', API_SECRET_KEY . '|' . $expiry, API_SECRET_KEY);
                if (hash_equals($expected, $tokenHash)) {
                    $isAuthorized = true;
                }
            }
        }
    }
}

if (!$isAuthorized) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// ── GET: info profil ──
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'success'      => true,
        'username'     => 'admin',
        'using_hash'   => m2b_admin_stored_hash() !== null,
        'updated_at'   => m2b_admin_password_updated_at(),
        'min_length'   => M2B_ADMIN_MIN_PASSWORD,
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// ── Rate limit: 5 percobaan per IP per 15 menit ──
// Token sesi bisa saja dicuri; ini menahan tebakan password lama secara beruntun.
$tempDir = __DIR__ . '/../temp';
if (!is_dir($tempDir)) {
    @mkdir($tempDir, 0755, true);
}
$rateFile = $tempDir . '/pwchg_' . md5($_SERVER['REMOTE_ADDR'] ?? 'cli') . '.json';
$rate = ['count' => 0, 'first' => time()];
if (file_exists($rateFile)) {
    $existing = json_decode(@file_get_contents($rateFile), true);
    if (is_array($existing) && isset($existing['count'], $existing['first'])) {
        if ($existing['count'] >= 5 && (time() - $existing['first']) < 900) {
            http_response_code(429);
            echo json_encode(['success' => false, 'message' => 'Terlalu banyak percobaan. Coba lagi dalam 15 menit.']);
            exit;
        }
        $rate = (time() - $existing['first']) >= 900
            ? ['count' => 0, 'first' => time()]
            : $existing;
    }
}

$input = json_decode(file_get_contents('php://input'), true);
$current = (string) ($input['current_password'] ?? '');
$new     = (string) ($input['new_password'] ?? '');

if ($current === '' || $new === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password lama dan baru wajib diisi.']);
    exit;
}

if (!m2b_admin_verify($current)) {
    $rate['count']++;
    @file_put_contents($rateFile, json_encode($rate));
    error_log('Gagal ganti password admin (password lama salah) dari ' . ($_SERVER['REMOTE_ADDR'] ?? '-'));
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Password lama salah.']);
    exit;
}

if (hash_equals($current, $new)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password baru harus berbeda dari yang lama.']);
    exit;
}

$problem = m2b_admin_password_problem($new);
if ($problem !== null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $problem]);
    exit;
}

$error = null;
if (!m2b_admin_set_password($new, $error)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $error ?: 'Gagal menyimpan password baru.']);
    exit;
}

@unlink($rateFile);
error_log('Password admin berhasil diubah dari ' . ($_SERVER['REMOTE_ADDR'] ?? '-'));

echo json_encode([
    'success'    => true,
    'message'    => 'Password berhasil diubah. Silakan login ulang dengan password baru.',
    'updated_at' => m2b_admin_password_updated_at(),
]);
