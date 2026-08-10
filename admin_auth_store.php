<?php
/**
 * M2B E-book — Sumber kebenaran password admin
 *
 * Urutan pencarian hash (yang pertama ketemu dipakai):
 *   1. admin_auth.php   — ditulis dari UI "Profil Admin". Bisa diubah tanpa deploy.
 *   2. ADMIN_PASSWORD_HASH di config.php — bila di-set manual.
 *   3. ADMIN_PASSWORD di config.php — password lama berbentuk teks (legacy).
 *
 * Begitu (1) atau (2) ada, password legacy otomatis tidak berlaku lagi.
 * File ini dan admin_auth.php diblokir dari web lewat .htaccess.
 */

if (!defined('M2B_ADMIN_MIN_PASSWORD')) {
    define('M2B_ADMIN_MIN_PASSWORD', 10);
}

function m2b_admin_auth_file(): string
{
    return __DIR__ . '/admin_auth.php';
}

/** Hash bcrypt yang berlaku saat ini, atau null bila masih memakai password legacy. */
function m2b_admin_stored_hash(): ?string
{
    $file = m2b_admin_auth_file();
    if (is_readable($file)) {
        $data = @include $file;
        if (is_array($data) && !empty($data['hash']) && is_string($data['hash'])) {
            return $data['hash'];
        }
    }
    if (defined('ADMIN_PASSWORD_HASH') && ADMIN_PASSWORD_HASH !== '') {
        return ADMIN_PASSWORD_HASH;
    }
    return null;
}

/** Kapan password terakhir diubah lewat UI (string ISO-8601), atau null. */
function m2b_admin_password_updated_at(): ?string
{
    $file = m2b_admin_auth_file();
    if (is_readable($file)) {
        $data = @include $file;
        if (is_array($data) && !empty($data['updated_at'])) {
            return (string) $data['updated_at'];
        }
    }
    return null;
}

/** Verifikasi password. Timing-safe untuk kedua jalur. */
function m2b_admin_verify(string $password): bool
{
    $hash = m2b_admin_stored_hash();
    if ($hash !== null) {
        return password_verify($password, $hash);
    }
    if (defined('ADMIN_PASSWORD')) {
        return hash_equals(ADMIN_PASSWORD, $password);
    }
    return false;
}

/**
 * Cek kekuatan password baru. Mengembalikan pesan kesalahan, atau null bila lolos.
 */
function m2b_admin_password_problem(string $password): ?string
{
    if (mb_strlen($password) < M2B_ADMIN_MIN_PASSWORD) {
        return 'Password baru minimal ' . M2B_ADMIN_MIN_PASSWORD . ' karakter.';
    }
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        return 'Password baru harus memuat huruf dan angka.';
    }
    // Tolak pola yang mudah ditebak untuk situs ini.
    $lower = mb_strtolower($password);
    foreach (['m2b', 'admin', 'ebook', 'password', 'ekspor', 'impor'] as $kata) {
        if (str_contains($lower, $kata)) {
            return 'Password baru terlalu mudah ditebak — hindari kata "' . $kata . '".';
        }
    }
    return null;
}

/**
 * Simpan password baru sebagai hash bcrypt. Penulisan atomik
 * (tulis ke berkas sementara lalu rename) supaya tidak pernah ada
 * kondisi berkas separuh tertulis yang bisa mengunci admin.
 */
function m2b_admin_set_password(string $newPassword, ?string &$error = null): bool
{
    $file = m2b_admin_auth_file();
    $hash = password_hash($newPassword, PASSWORD_BCRYPT);
    if (!is_string($hash) || $hash === '') {
        $error = 'Gagal membuat hash password.';
        return false;
    }

    $payload = [
        'hash'       => $hash,
        'updated_at' => date('c'),
    ];
    $content = "<?php\n"
        . "// Dibuat otomatis oleh api/admin_change_password.php — jangan diedit manual.\n"
        . "// Diblokir dari akses web lewat .htaccess dan tidak ikut ter-commit (.gitignore).\n"
        . 'return ' . var_export($payload, true) . ";\n";

    $tmp = $file . '.tmp' . bin2hex(random_bytes(4));
    if (@file_put_contents($tmp, $content, LOCK_EX) === false) {
        $error = 'Gagal menulis berkas password. Periksa izin tulis direktori.';
        return false;
    }
    @chmod($tmp, 0600);

    // Pastikan berkas sementara benar-benar bisa di-parse sebelum menimpa yang lama.
    $check = @include $tmp;
    if (!is_array($check) || empty($check['hash']) || !password_verify($newPassword, $check['hash'])) {
        @unlink($tmp);
        $error = 'Verifikasi berkas password gagal, perubahan dibatalkan.';
        return false;
    }

    if (!@rename($tmp, $file)) {
        @unlink($tmp);
        $error = 'Gagal memasang berkas password baru.';
        return false;
    }

    // Tanpa ini, opcache bisa terus menyajikan hash lama.
    if (function_exists('opcache_invalidate')) {
        @opcache_invalidate($file, true);
    }

    return true;
}
