<?php
/**
 * Script untuk memeriksa postingan di database M2B.
 * Jalankan: php check_posts.php
 */

echo "=== MEMERIKSA POSTINGAN DATABASE M2B ===\n";

$dbHost = '127.0.0.1';
$dbName = 'u301249154_new_m2b';
$dbUser = 'u301249154_new_m2b_user';
$dbPass = 'rootFawFaq34*';

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Terhubung ke database MySQL M2B.\n\n";

    $stmt = $pdo->query("SELECT id, title, slug, category, status, featured_image, published_at FROM posts ORDER BY id DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Total Post: " . count($posts) . "\n";
    echo str_repeat("-", 80) . "\n";
    echo sprintf("%-4s | %-30s | %-12s | %-10s | %-15s\n", "ID", "Title", "Category", "Status", "Pub Date");
    echo str_repeat("-", 80) . "\n";

    foreach ($posts as $post) {
        $title = mb_strimwidth($post['title'], 0, 30, "...");
        echo sprintf("%-4d | %-30s | %-12s | %-10s | %-15s\n", 
            $post['id'], 
            $title ?: "[KOSONG/EMPTY]", 
            $post['category'] ?: "[KOSONG]", 
            $post['status'], 
            $post['published_at']
        );
        if (empty($post['title']) || empty($post['slug'])) {
            echo "   ⚠️ WARNING: Post ID " . $post['id'] . " memiliki title atau slug kosong!\n";
        }
    }
    echo str_repeat("-", 80) . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
