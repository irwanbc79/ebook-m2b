<?php
/**
 * M2B Admin Orders API
 * Fetch, search, and manage orders from MySQL database
 * 
 * GET  /api/admin_orders.php              → List all orders
 * GET  /api/admin_orders.php?status=pending → Filter by status
 * GET  /api/admin_orders.php?search=keyword → Search orders
 * GET  /api/admin_orders.php?id=M2B-xxx    → Get single order
 * POST /api/admin_orders.php               → Update order (notes, delete)
 * 
 * Headers: Authorization: Bearer <API_SECRET_KEY>
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config.php';

// ── Auth ──
$headers = getallheaders();
$apiKey = '';
if (isset($headers['Authorization'])) {
    $apiKey = str_replace('Bearer ', '', $headers['Authorization']);
} elseif (isset($headers['authorization'])) {
    $apiKey = str_replace('Bearer ', '', $headers['authorization']);
} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $apiKey = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
}

if (empty($apiKey) || $apiKey !== API_SECRET_KEY) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// ── DB Connection ──
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// ── GET: Fetch Orders ──
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Single order by ID
    if (!empty($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
        $stmt->execute([$_GET['id']]);
        $order = $stmt->fetch();
        if ($order) {
            echo json_encode(['success' => true, 'order' => $order]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Order not found']);
        }
        exit;
    }

    // Build query with filters
    $where = [];
    $params = [];

    if (!empty($_GET['status']) && $_GET['status'] !== 'all') {
        $where[] = "payment_status = ?";
        $params[] = $_GET['status'];
    }

    if (!empty($_GET['search'])) {
        $search = '%' . $_GET['search'] . '%';
        $where[] = "(order_id LIKE ? OR buyer_name LIKE ? OR buyer_email LIKE ? OR buyer_whatsapp LIKE ? OR buyer_city LIKE ?)";
        $params = array_merge($params, [$search, $search, $search, $search, $search]);
    }

    $sql = "SELECT * FROM orders";
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY created_at DESC";

    // Pagination
    $page = max(1, intval($_GET['page'] ?? 1));
    $perPage = max(1, min(100, intval($_GET['per_page'] ?? 20)));
    $offset = ($page - 1) * $perPage;

    // Get total count
    $countSql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];

    // Get paginated results
    $sql .= " LIMIT $perPage OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();

    // Get stats
    $statsStmt = $pdo->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN payment_status = 'verified' THEN 1 ELSE 0 END) as verified,
            SUM(CASE WHEN payment_status = 'failed' THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN payment_status = 'verified' THEN 1 ELSE 0 END) * " . EBOOK_PRICE . " as revenue
        FROM orders
    ");
    $stats = $statsStmt->fetch();

    echo json_encode([
        'success' => true,
        'orders' => $orders,
        'stats' => $stats,
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => intval($total),
            'total_pages' => ceil($total / $perPage)
        ]
    ]);
    exit;
}

// ── POST: Update Order ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    $orderId = $input['order_id'] ?? '';

    if (empty($orderId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'order_id is required']);
        exit;
    }

    switch ($action) {
        case 'add_note':
            $note = $input['note'] ?? '';
            $stmt = $pdo->prepare("UPDATE orders SET notes = CONCAT(COALESCE(notes,''), ?) WHERE order_id = ?");
            $stmt->execute(["\n[" . date('Y-m-d H:i') . "] " . $note, $orderId]);
            echo json_encode(['success' => true, 'message' => 'Note added']);
            break;

        case 'delete':
            $stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = ?");
            $stmt->execute([$orderId]);
            echo json_encode(['success' => true, 'message' => 'Order deleted']);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
