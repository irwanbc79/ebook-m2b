<?php
/**
 * M2B Admin Payment Proof API
 * Upload, retrieve, and delete payment proof images
 * 
 * POST   /api/payment_proof.php  → Upload proof (multipart/form-data)
 * GET    /api/payment_proof.php?order_id=xxx → Get proof image
 * DELETE /api/payment_proof.php  → Delete proof
 * 
 * Headers: Authorization: Bearer <API_SECRET_KEY>
 */

header('Access-Control-Allow-Origin: https://ebook.m2b.co.id');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config.php';

// ── Auth (skip for GET with valid token param) ──
$headers = getallheaders();
$apiKey = '';
if (isset($headers['Authorization'])) {
    $apiKey = str_replace('Bearer ', '', $headers['Authorization']);
} elseif (isset($headers['authorization'])) {
    $apiKey = str_replace('Bearer ', '', $headers['authorization']);
} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $apiKey = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
}

// For GET (image serving), allow token via query param
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($apiKey) && !empty($_GET['token'])) {
    $apiKey = $_GET['token'];
}

if (empty($apiKey) || $apiKey !== API_SECRET_KEY) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// ── DB Connection ──
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Ensure payment_proof column exists
try {
    $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_proof VARCHAR(255) DEFAULT NULL");
} catch (PDOException $ignore) {}

$uploadDir = __DIR__ . '/../uploads/proofs/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// ── GET: Serve proof image ──
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $orderId = $_GET['order_id'] ?? '';
    if (empty($orderId)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'order_id required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT payment_proof FROM orders WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $row = $stmt->fetch();

    if (!$row || empty($row['payment_proof'])) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No payment proof found']);
        exit;
    }

    $filePath = $uploadDir . $row['payment_proof'];
    if (!file_exists($filePath)) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'File not found on disk']);
        exit;
    }

    // Serve image directly
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($filePath);
    
    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mimeType, $allowedMimes)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid file type']);
        exit;
    }

    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: private, max-age=3600');
    readfile($filePath);
    exit;
}

// ── POST: Upload proof ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $orderId = $_POST['order_id'] ?? '';
    if (empty($orderId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'order_id required']);
        exit;
    }

    // Verify order exists
    $stmt = $pdo->prepare("SELECT order_id, payment_proof FROM orders WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if (!$order) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }

    // Validate file upload
    if (empty($_FILES['proof']) || $_FILES['proof']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (maks server)',
            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar',
            UPLOAD_ERR_PARTIAL => 'Upload tidak selesai',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang dipilih',
        ];
        $errCode = $_FILES['proof']['error'] ?? UPLOAD_ERR_NO_FILE;
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $errorMessages[$errCode] ?? 'Upload gagal']);
        exit;
    }

    $file = $_FILES['proof'];

    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'File terlalu besar. Maksimal 5MB.']);
        exit;
    }

    // Validate MIME type by reading file content
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    if (!isset($allowedMimes[$mimeType])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.']);
        exit;
    }

    $ext = $allowedMimes[$mimeType];

    // Delete old proof file if exists
    if (!empty($order['payment_proof'])) {
        $oldFile = $uploadDir . $order['payment_proof'];
        if (file_exists($oldFile)) {
            unlink($oldFile);
        }
    }

    // Generate safe filename
    $safeOrderId = preg_replace('/[^A-Za-z0-9\-]/', '', $orderId);
    $filename = 'proof_' . $safeOrderId . '_' . time() . '.' . $ext;
    $destPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan file']);
        exit;
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE orders SET payment_proof = ? WHERE order_id = ?");
    $stmt->execute([$filename, $orderId]);

    // Log
    $noteStmt = $pdo->prepare("UPDATE orders SET notes = CONCAT(COALESCE(notes,''), ?) WHERE order_id = ?");
    $noteStmt->execute(["\n[" . date('Y-m-d H:i') . "] 📸 Bukti bayar di-upload", $orderId]);

    error_log("Payment proof uploaded for {$orderId}: {$filename}");

    echo json_encode([
        'success' => true,
        'message' => 'Bukti bayar berhasil di-upload',
        'filename' => $filename
    ]);
    exit;
}

// ── DELETE: Remove proof ──
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);
    $orderId = $input['order_id'] ?? '';

    if (empty($orderId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'order_id required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT payment_proof FROM orders WHERE order_id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if ($order && !empty($order['payment_proof'])) {
        $filePath = $uploadDir . $order['payment_proof'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $stmt = $pdo->prepare("UPDATE orders SET payment_proof = NULL WHERE order_id = ?");
        $stmt->execute([$orderId]);
    }

    echo json_encode(['success' => true, 'message' => 'Bukti bayar dihapus']);
    exit;
}

http_response_code(405);
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
