<?php
/**
 * M2B E-book Admin Dashboard
 * Manage orders and payment verification
 * 
 * Updated: January 2026 - Price: Rp 49.000
 */

session_start();
require_once '../config.php';

// Session timeout (2 hours)
if (isset($_SESSION['admin_logged_in']) && isset($_SESSION['login_time'])) {
    if (time() - $_SESSION['login_time'] > 7200) {
        session_destroy();
        session_start();
    }
}

// Authentication
$admin_password = defined('ADMIN_PASSWORD') ? ADMIN_PASSWORD : 'm2b_admin_2024';

if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $login_error = 'Sesi tidak valid, silakan coba lagi.';
        } elseif (hash_equals($admin_password, $_POST['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_time'] = time();
        } else {
            $login_error = 'Password salah!';
        }
    }
    
    // Generate CSRF token for login form
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    if (!isset($_SESSION['admin_logged_in'])) {
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login Admin - M2B E-book</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { 
                    font-family: 'Segoe UI', system-ui, sans-serif; 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                    min-height: 100vh; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center;
                    padding: 20px;
                }
                .login-box { 
                    background: white; 
                    padding: 40px; 
                    border-radius: 16px; 
                    box-shadow: 0 20px 40px rgba(0,0,0,0.2); 
                    max-width: 400px; 
                    width: 100%;
                }
                .login-box h1 { 
                    color: #333; 
                    margin-bottom: 8px; 
                    font-size: 28px;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .login-box p { 
                    color: #666; 
                    margin-bottom: 30px;
                    font-size: 14px;
                }
                .form-group { margin-bottom: 20px; }
                .form-group label { 
                    display: block; 
                    margin-bottom: 8px; 
                    color: #333; 
                    font-weight: 500;
                }
                .form-group input { 
                    width: 100%; 
                    padding: 14px 16px; 
                    border: 2px solid #e5e7eb; 
                    border-radius: 8px; 
                    font-size: 16px;
                    transition: border-color 0.3s;
                }
                .form-group input:focus { 
                    outline: none; 
                    border-color: #667eea;
                }
                button { 
                    width: 100%; 
                    padding: 14px; 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                    color: white; 
                    border: none; 
                    border-radius: 8px; 
                    font-size: 16px; 
                    font-weight: 600;
                    cursor: pointer;
                    transition: transform 0.2s, box-shadow 0.2s;
                }
                button:hover { 
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
                }
                .error { 
                    background: #fef2f2; 
                    color: #dc2626; 
                    padding: 12px 16px; 
                    border-radius: 8px; 
                    margin-bottom: 20px;
                    font-size: 14px;
                }
                .logo { 
                    text-align: center; 
                    margin-bottom: 30px;
                }
                .logo span { 
                    font-size: 36px; 
                    font-weight: 800;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
            </style>
        </head>
        <body>
            <div class="login-box">
                <div class="logo"><span>M2B</span></div>
                <h1>🔐 Admin Login</h1>
                <p>Masuk ke dashboard admin M2B E-book</p>
                <?php if (isset($login_error)): ?>
                    <div class="error"><?= htmlspecialchars($login_error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan password admin" required autofocus>
                    </div>
                    <button type="submit">Masuk</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: dashboard.php');
    exit;
}

// Database connection
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die('<div style="padding:20px;color:#dc2626;">Database Error: ' . htmlspecialchars($e->getMessage()) . '</div>');
}

// Get statistics
$stats = [
    'total' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?: 0,
    'pending' => $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status='pending'")->fetchColumn() ?: 0,
    'verified' => $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status='verified'")->fetchColumn() ?: 0,
    'failed' => $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status='failed'")->fetchColumn() ?: 0,
    'revenue' => $pdo->query("SELECT COUNT(*) * " . EBOOK_PRICE . " FROM orders WHERE payment_status='verified'")->fetchColumn() ?: 0
];

// Get orders with pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$whereClause = '';
$queryParams = [];
if ($statusFilter && in_array($statusFilter, ['pending', 'verified', 'failed'])) {
    $whereClause = "WHERE payment_status = ?";
    $queryParams[] = $statusFilter;
}

$orderStmt = $pdo->prepare("SELECT * FROM orders {$whereClause} ORDER BY created_at DESC LIMIT ? OFFSET ?");
$paramIndex = 1;
foreach ($queryParams as $p) {
    $orderStmt->bindValue($paramIndex++, $p);
}
$orderStmt->bindValue($paramIndex++, $perPage, PDO::PARAM_INT);
$orderStmt->bindValue($paramIndex, $offset, PDO::PARAM_INT);
$orderStmt->execute();
$orders = $orderStmt->fetchAll();

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM orders {$whereClause}");
$countStmt->execute($queryParams);
$totalOrders = $countStmt->fetchColumn();
$totalPages = ceil($totalOrders / $perPage);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - M2B E-book</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', system-ui, sans-serif; 
            background: #f5f7fa; 
            color: #1f2937;
            min-height: 100vh;
        }
        
        /* Header */
        .header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 20px 30px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header h1 { 
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header-actions { display: flex; gap: 15px; align-items: center; }
        .btn-logout { 
            padding: 10px 20px; 
            background: rgba(255,255,255,0.2); 
            color: white; 
            border: 1px solid rgba(255,255,255,0.3); 
            border-radius: 8px; 
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s;
        }
        .btn-logout:hover { background: rgba(255,255,255,0.3); }
        
        /* Container */
        .container { max-width: 1400px; margin: 0 auto; padding: 30px; }
        
        /* Stats Grid */
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px;
        }
        .stat-card { 
            background: white; 
            padding: 24px; 
            border-radius: 12px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card.total { border-left-color: #667eea; }
        .stat-card.pending { border-left-color: #f59e0b; }
        .stat-card.verified { border-left-color: #10b981; }
        .stat-card.failed { border-left-color: #ef4444; }
        .stat-card.revenue { border-left-color: #8b5cf6; }
        .stat-card h3 { color: #6b7280; font-size: 14px; font-weight: 500; margin-bottom: 8px; }
        .stat-card .value { font-size: 32px; font-weight: 700; color: #1f2937; }
        .stat-card .value.revenue { color: #8b5cf6; }
        
        /* Filter Tabs */
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .filter-tab {
            padding: 10px 20px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            text-decoration: none;
            color: #6b7280;
            font-weight: 500;
            transition: all 0.3s;
        }
        .filter-tab:hover { border-color: #667eea; color: #667eea; }
        .filter-tab.active { 
            background: #667eea; 
            border-color: #667eea; 
            color: white;
        }
        
        /* Orders Section */
        .orders-section { 
            background: white; 
            border-radius: 12px; 
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .section-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px;
        }
        .section-header h2 { font-size: 20px; color: #1f2937; }
        
        /* Table */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th { 
            background: #f9fafb; 
            padding: 14px 16px; 
            text-align: left; 
            font-weight: 600; 
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td { 
            padding: 16px; 
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }
        tr:hover { background: #f9fafb; }
        
        /* Status Badges */
        .status-badge { 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 600;
            display: inline-block;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-verified { background: #d1fae5; color: #065f46; }
        .status-failed { background: #fee2e2; color: #991b1b; }
        
        /* Action Buttons */
        .action-buttons { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn { 
            padding: 8px 16px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 13px; 
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-approve { background: #10b981; color: white; }
        .btn-approve:hover:not(:disabled) { background: #059669; }
        .btn-reject { background: #ef4444; color: white; }
        .btn-reject:hover:not(:disabled) { background: #dc2626; }
        .btn-wa { 
            background: #25d366; 
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }
        .btn-wa:hover { background: #128c7e; }
        
        /* Order ID */
        .order-id { 
            font-family: 'Consolas', monospace; 
            font-weight: 600;
            color: #667eea;
        }
        
        /* Buyer Info */
        .buyer-name { font-weight: 600; color: #1f2937; }
        .buyer-email { font-size: 12px; color: #6b7280; }
        .buyer-wa { font-size: 12px; color: #6b7280; }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 24px;
        }
        .pagination a {
            padding: 10px 16px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            text-decoration: none;
            color: #374151;
            font-weight: 500;
            transition: all 0.2s;
        }
        .pagination a:hover { border-color: #667eea; color: #667eea; }
        .pagination a.active { background: #667eea; border-color: #667eea; color: white; }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        .empty-state .icon { font-size: 48px; margin-bottom: 16px; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header { flex-direction: column; gap: 15px; text-align: center; }
            .container { padding: 20px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 M2B Admin Dashboard</h1>
        <div class="header-actions">
            <span>Harga: Rp <?= number_format(EBOOK_PRICE, 0, ',', '.') ?></span>
            <a href="<?= SITE_URL ?>" target="_blank" class="btn-logout">🌐 Lihat Website</a>
            <a href="?logout=1" class="btn-logout">🚪 Keluar</a>
        </div>
    </div>
    
    <div class="container">
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card total">
                <h3>📦 Total Pesanan</h3>
                <div class="value"><?= number_format($stats['total']) ?></div>
            </div>
            <div class="stat-card pending">
                <h3>⏳ Menunggu Verifikasi</h3>
                <div class="value"><?= number_format($stats['pending']) ?></div>
            </div>
            <div class="stat-card verified">
                <h3>✅ Terverifikasi</h3>
                <div class="value"><?= number_format($stats['verified']) ?></div>
            </div>
            <div class="stat-card failed">
                <h3>❌ Ditolak</h3>
                <div class="value"><?= number_format($stats['failed']) ?></div>
            </div>
            <div class="stat-card revenue">
                <h3>💰 Total Pendapatan</h3>
                <div class="value revenue">Rp <?= number_format($stats['revenue'], 0, ',', '.') ?></div>
            </div>
        </div>
        
        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="?status=" class="filter-tab <?= $statusFilter === '' ? 'active' : '' ?>">Semua (<?= $stats['total'] ?>)</a>
            <a href="?status=pending" class="filter-tab <?= $statusFilter === 'pending' ? 'active' : '' ?>">Pending (<?= $stats['pending'] ?>)</a>
            <a href="?status=verified" class="filter-tab <?= $statusFilter === 'verified' ? 'active' : '' ?>">Verified (<?= $stats['verified'] ?>)</a>
            <a href="?status=failed" class="filter-tab <?= $statusFilter === 'failed' ? 'active' : '' ?>">Failed (<?= $stats['failed'] ?>)</a>
        </div>
        
        <!-- Orders Table -->
        <div class="orders-section">
            <div class="section-header">
                <h2>📋 Daftar Pesanan</h2>
            </div>
            
            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <div class="icon">📭</div>
                    <p>Belum ada pesanan<?= $statusFilter ? " dengan status '{$statusFilter}'" : '' ?></p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Pembeli</th>
                                <th>Kota</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr id="order-<?= htmlspecialchars($order['order_id']) ?>">
                                <td>
                                    <span class="order-id"><?= htmlspecialchars($order['order_id']) ?></span>
                                </td>
                                <td>
                                    <div class="buyer-name"><?= htmlspecialchars($order['buyer_name']) ?></div>
                                    <div class="buyer-email"><?= htmlspecialchars($order['buyer_email']) ?></div>
                                    <div class="buyer-wa"><?= htmlspecialchars($order['buyer_whatsapp']) ?></div>
                                </td>
                                <td><?= htmlspecialchars($order['buyer_city']) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $order['payment_status'] ?>">
                                        <?= ucfirst($order['payment_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                                    <div style="font-size:12px;color:#6b7280"><?= date('H:i', strtotime($order['created_at'])) ?></div>
                                </td>
                                <td>
                                    <?php if ($order['payment_status'] === 'pending'): ?>
                                        <div class="action-buttons">
                                            <button class="btn btn-approve" onclick="verifyPayment('<?= $order['order_id'] ?>', 'approve')">
                                                ✓ Approve
                                            </button>
                                            <button class="btn btn-reject" onclick="verifyPayment('<?= $order['order_id'] ?>', 'reject')">
                                                ✗ Reject
                                            </button>
                                        </div>
                                    <?php elseif ($order['payment_status'] === 'verified'): ?>
                                        <a href="https://wa.me/<?= htmlspecialchars($order['buyer_whatsapp']) ?>" target="_blank" class="btn-wa">
                                            💬 Kirim WA
                                        </a>
                                    <?php else: ?>
                                        <span style="color:#6b7280">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?><?= $statusFilter ? "&status={$statusFilter}" : '' ?>" 
                           class="<?= $page === $i ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
    const API_SECRET_KEY = '<?= API_SECRET_KEY ?>';
    
    async function verifyPayment(orderId, action) {
        const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
        if (!confirm(`Yakin ingin ${actionText} pesanan ${orderId}?`)) {
            return;
        }
        
        const row = document.getElementById('order-' + orderId);
        const buttons = row.querySelectorAll('button');
        buttons.forEach(btn => {
            btn.disabled = true;
            btn.textContent = 'Memproses...';
        });
        
        try {
            const response = await fetch('../api/verify_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + API_SECRET_KEY
                },
                body: JSON.stringify({
                    order_id: orderId,
                    action: action
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert(`Pesanan berhasil di${action === 'approve' ? 'setujui' : 'tolak'}!`);
                
                // Update UI
                const badge = row.querySelector('.status-badge');
                const actionCell = row.querySelector('.action-buttons');
                
                if (action === 'approve') {
                    badge.className = 'status-badge status-verified';
                    badge.textContent = 'Verified';
                    
                    if (data.whatsapp_url && actionCell) {
                        actionCell.innerHTML = `<a href="${data.whatsapp_url}" target="_blank" class="btn-wa">💬 Kirim WA</a>`;
                    }
                } else {
                    badge.className = 'status-badge status-failed';
                    badge.textContent = 'Failed';
                    if (actionCell) {
                        actionCell.innerHTML = '<span style="color:#6b7280">-</span>';
                    }
                }
                
                // Reload after 2 seconds
                setTimeout(() => location.reload(), 2000);
                
            } else {
                alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                buttons.forEach((btn, i) => {
                    btn.disabled = false;
                    btn.textContent = i === 0 ? '✓ Approve' : '✗ Reject';
                });
            }
            
        } catch (error) {
            console.error('Error:', error);
            alert('Network error: ' + error.message);
            buttons.forEach((btn, i) => {
                btn.disabled = false;
                btn.textContent = i === 0 ? '✓ Approve' : '✗ Reject';
            });
        }
    }
    </script>
</body>
</html>
