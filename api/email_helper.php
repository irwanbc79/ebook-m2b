<?php
/**
 * M2B E-book Email Helper
 * Sends HTML email confirmations and e-book delivery
 * 
 * Updated: January 2026 - Price: Rp 49.000
 */

class EmailHelper {
    private $from_email;
    private $from_name;
    private $reply_to;
    
    public function __construct() {
        $this->from_email = defined('FROM_EMAIL') ? FROM_EMAIL : 'ebook@m2b.co.id';
        $this->from_name = defined('FROM_NAME') ? FROM_NAME : 'M2B E-book System';
        $this->reply_to = defined('SUPPORT_EMAIL') ? SUPPORT_EMAIL : 'ebook@m2b.co.id';
    }
    
    /**
     * Send order confirmation email (after form submission)
     */
    public function sendOrderConfirmation($orderData) {
        $to = $orderData['buyer_email'];
        $subject = "Konfirmasi Pesanan E-book - Order ID: {$orderData['order_id']}";
        $message = $this->getOrderConfirmationHTML($orderData);
        
        return $this->sendEmail($to, $subject, $message);
    }
    
    /**
     * Send e-book delivery email (after payment approval)
     */
    public function sendEbookDelivery($orderData) {
        $to = $orderData['buyer_email'];
        $subject = "✅ Pembayaran Terverifikasi - Download E-book Anda";
        $message = $this->getEbookDeliveryHTML($orderData);
        
        return $this->sendEmail($to, $subject, $message);
    }
    /**
     * Send payment rejection email
     */
    public function sendPaymentRejection($orderData) {
        $to = $orderData['buyer_email'];
        $subject = "❌ Informasi Pembayaran - Order {$orderData['order_id']}";
        $message = $this->getPaymentRejectionHTML($orderData);
        
        return $this->sendEmail($to, $subject, $message);
    }
    
    /**
     * Send payment reminder email
     */
    public function sendPaymentReminder($orderData) {
        $to = $orderData['buyer_email'];
        $subject = "⏰ Reminder: Selesaikan Pembayaran E-book - {$orderData['order_id']}";
        $message = $this->getPaymentReminderHTML($orderData);
        
        return $this->sendEmail($to, $subject, $message);
    }
    
    /**
     * Order Confirmation Email Template
     */
    private function getOrderConfirmationHTML($data) {
        $price = defined('EBOOK_PRICE') ? EBOOK_PRICE : 49000;
        $bankName = defined('BANK_NAME') ? BANK_NAME : 'BCA';
        $bankAccount = defined('BANK_ACCOUNT') ? BANK_ACCOUNT : '8280424243';
        $bankHolder = defined('BANK_HOLDER') ? BANK_HOLDER : 'Eka Mayang Sari Harahap';
        $adminWa = defined('ADMIN_WHATSAPP') ? ADMIN_WHATSAPP : '6282261846811';
        
        $priceFormatted = 'Rp ' . number_format($price, 0, ',', '.');
        
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan E-book M2B</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;line-height:1.6;color:#333;background:#f5f5f5;">
    <div style="max-width:600px;margin:0 auto;padding:20px;">
        <!-- Header -->
        <div style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:40px 30px;text-align:center;border-radius:16px 16px 0 0;">
            <h1 style="margin:0;font-size:28px;">📚 Pesanan E-book Diterima!</h1>
        </div>
        
        <!-- Content -->
        <div style="background:white;padding:40px 30px;border-radius:0 0 16px 16px;">
            <p style="font-size:16px;">Halo <strong>{$data['buyer_name']}</strong>,</p>
            
            <p>Terima kasih telah memesan <strong>"Panduan Global Export & Import untuk Indonesia"</strong> dari M2B E-book.</p>
            
            <!-- Order Details -->
            <div style="background:#f8fafc;border:2px solid #667eea;border-radius:12px;padding:24px;margin:24px 0;">
                <h3 style="margin:0 0 16px 0;color:#667eea;">📋 Detail Pesanan</h3>
                <table style="width:100%;border-collapse:collapse;">
                    <tr>
                        <td style="padding:8px 0;color:#666;">Order ID:</td>
                        <td style="padding:8px 0;text-align:right;font-weight:600;">{$data['order_id']}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#666;">Nama:</td>
                        <td style="padding:8px 0;text-align:right;">{$data['buyer_name']}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#666;">Email:</td>
                        <td style="padding:8px 0;text-align:right;">{$data['buyer_email']}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#666;">WhatsApp:</td>
                        <td style="padding:8px 0;text-align:right;">{$data['buyer_whatsapp']}</td>
                    </tr>
                    <tr style="border-top:1px dashed #ddd;">
                        <td style="padding:12px 0 0 0;color:#666;font-weight:600;">Harga:</td>
                        <td style="padding:12px 0 0 0;text-align:right;font-size:24px;font-weight:700;color:#667eea;">{$priceFormatted}</td>
                    </tr>
                </table>
            </div>
            
            <!-- Payment Info -->
            <div style="background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:8px;padding:20px;margin:24px 0;">
                <h3 style="margin:0 0 12px 0;color:#92400e;">💳 Informasi Pembayaran</h3>
                <p style="margin:4px 0;"><strong>Bank:</strong> {$bankName}</p>
                <p style="margin:4px 0;"><strong>No. Rekening:</strong> <span style="font-size:20px;font-family:monospace;color:#000;">{$bankAccount}</span></p>
                <p style="margin:4px 0;"><strong>Atas Nama:</strong> {$bankHolder}</p>
                <p style="margin:12px 0 0 0;"><strong>Jumlah Transfer:</strong> <span style="font-size:20px;color:#dc2626;font-weight:700;">{$priceFormatted}</span></p>
            </div>
            
            <!-- Steps -->
            <h3 style="color:#333;">📝 Langkah Selanjutnya:</h3>
            <ol style="padding-left:20px;color:#555;">
                <li style="margin:8px 0;">Transfer sejumlah <strong>{$priceFormatted}</strong> ke rekening di atas</li>
                <li style="margin:8px 0;">Setelah transfer, kirim bukti pembayaran via WhatsApp ke <strong>+62 822-6184-6811</strong></li>
                <li style="margin:8px 0;">Sertakan <strong>Order ID: {$data['order_id']}</strong> dalam pesan WhatsApp</li>
                <li style="margin:8px 0;">Tim kami akan verifikasi pembayaran (maks. 2 jam)</li>
                <li style="margin:8px 0;">E-book akan dikirim ke email ini setelah pembayaran terverifikasi</li>
            </ol>
            
            <!-- CTA Button -->
            <div style="text-align:center;margin:32px 0;">
                <a href="https://wa.me/{$adminWa}?text=Halo%2C%20saya%20sudah%20transfer%20untuk%20Order%20ID%3A%20{$data['order_id']}" 
                   style="display:inline-block;padding:16px 32px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;text-decoration:none;border-radius:8px;font-weight:600;font-size:16px;">
                    💬 Konfirmasi via WhatsApp
                </a>
            </div>
            
            <p style="color:#666;font-size:14px;margin-top:32px;font-style:italic;">
                Email ini dikirim otomatis oleh sistem M2B E-book. Jika Anda memiliki pertanyaan, silakan hubungi kami via WhatsApp atau email.
            </p>
        </div>
        
        <!-- Footer -->
        <div style="text-align:center;padding:24px;color:#666;font-size:13px;">
            <p style="margin:4px 0;"><strong>M2B Global Trade Academy</strong></p>
            <p style="margin:4px 0;">📧 ebook@m2b.co.id | 📱 +62 822-6184-6811</p>
            <p style="margin:4px 0;">🌐 <a href="https://m2b.co.id" style="color:#667eea;">m2b.co.id</a> | 💬 <a href="https://t.me/+vLwFWh-xg54wMzNl" style="color:#667eea;">Telegram</a></p>
            <p style="margin-top:16px;color:#999;">© 2026 M2B. Hak Cipta Dilindungi.</p>
        </div>
    </div>
</body>
</html>
HTML;
        
        return $html;
    }
    
    /**
     * E-book Delivery Email Template
     */
    private function getEbookDeliveryHTML($data) {
        $downloadLink = isset($data['ebook_url']) ? $data['ebook_url'] : (defined('GOOGLE_DRIVE_LINK') ? GOOGLE_DRIVE_LINK : '#');
        $telegramGroup = defined('TELEGRAM_GROUP') ? TELEGRAM_GROUP : 'https://t.me/+vLwFWh-xg54wMzNl';
        
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-book Anda Siap - M2B</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;line-height:1.6;color:#333;background:#f5f5f5;">
    <div style="max-width:600px;margin:0 auto;padding:20px;">
        <!-- Header -->
        <div style="background:linear-gradient(135deg,#10b981 0%,#059669 100%);color:white;padding:40px 30px;text-align:center;border-radius:16px 16px 0 0;">
            <h1 style="margin:0;font-size:28px;">🎉 Pembayaran Terverifikasi!</h1>
        </div>
        
        <!-- Content -->
        <div style="background:white;padding:40px 30px;border-radius:0 0 16px 16px;">
            <!-- Success Box -->
            <div style="background:#d1fae5;border:2px solid #10b981;border-radius:12px;padding:24px;text-align:center;margin-bottom:24px;">
                <h2 style="color:#065f46;margin:0;">✅ Pembayaran Anda Telah Dikonfirmasi</h2>
                <p style="margin:8px 0 0 0;color:#047857;">Order ID: <strong>{$data['order_id']}</strong></p>
            </div>
            
            <p style="font-size:16px;">Halo <strong>{$data['buyer_name']}</strong>,</p>
            
            <p>Terima kasih atas pembayaran Anda! Kami dengan senang hati mengonfirmasi bahwa pembayaran untuk e-book <strong>"Panduan Global Export & Import untuk Indonesia"</strong> telah kami terima.</p>
            
            <!-- Download Box -->
            <div style="background:#f0fdf4;border:2px dashed #10b981;border-radius:12px;padding:32px;text-align:center;margin:24px 0;">
                <h2 style="margin:0 0 12px 0;color:#065f46;">📚 Download E-book Anda</h2>
                <p style="color:#666;margin:0 0 20px 0;">Klik tombol di bawah ini untuk mengunduh e-book:</p>
                <a href="{$downloadLink}" 
                   style="display:inline-block;padding:18px 40px;background:#10b981;color:white;text-decoration:none;border-radius:8px;font-weight:700;font-size:18px;">
                    📥 Download E-book Sekarang
                </a>
                <p style="color:#6b7280;font-size:13px;margin:20px 0 0 0;">
                    Link download berlaku selamanya. Simpan baik-baik!
                </p>
            </div>
            
            <!-- Watermark Info -->
            <div style="background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:8px;padding:20px;margin:24px 0;">
                <h3 style="margin:0 0 12px 0;color:#92400e;">🔖 Informasi Penting</h3>
                <p style="margin:4px 0;">E-book ini telah di-watermark khusus untuk Anda:</p>
                <ul style="margin:12px 0;padding-left:20px;">
                    <li><strong>Nama:</strong> {$data['buyer_name']}</li>
                    <li><strong>Email:</strong> {$data['buyer_email']}</li>
                </ul>
                <p style="color:#b45309;font-size:13px;margin:12px 0 0 0;">
                    ⚠️ E-book ini hanya untuk penggunaan pribadi. Dilarang mendistribusikan, menjual kembali, atau membagikan ke pihak lain tanpa izin.
                </p>
            </div>
            
            <!-- What You Learn -->
            <h3 style="color:#333;">📖 Apa yang Akan Anda Pelajari?</h3>
            <ul style="padding-left:20px;color:#555;">
                <li style="margin:8px 0;">22 Chapter lengkap tentang ekspor-impor (v2.0)</li>
                <li style="margin:8px 0;">Dari mindset hingga praktik langsung</li>
                <li style="margin:8px 0;">Dokumen, HS Code, strategi negosiasi</li>
                <li style="margin:8px 0;">Tips menekan biaya dan cara mencari buyer</li>
            </ul>
            
            <!-- Join Community -->
            <div style="background:#f0f9ff;border-radius:12px;padding:24px;text-align:center;margin:24px 0;">
                <h3 style="margin:0 0 12px 0;color:#0369a1;">💬 Gabung Komunitas!</h3>
                <p style="color:#666;margin:0 0 16px 0;">Diskusi dan networking dengan sesama pembaca e-book M2B</p>
                <a href="{$telegramGroup}" 
                   style="display:inline-block;padding:12px 24px;background:#0088cc;color:white;text-decoration:none;border-radius:8px;font-weight:600;">
                    🚀 Join Telegram Group
                </a>
            </div>
            
            <!-- Toolkit -->
            <div style="background:linear-gradient(135deg,#0b1d40,#192d60);border-radius:12px;padding:28px;text-align:center;margin:24px 0;">
                <h3 style="margin:0 0 12px 0;color:#f9fafb;">&#128295; Toolkit Global Trader</h3>
                <p style="color:#d1d5db;margin:0 0 16px 0;font-size:14px;">Akses toolkit interaktif: Checklist Kesiapan, Kalkulator Landed Cost, Incoterms 2020, Glossary, dan Referensi Link Resmi.</p>
                <a href="https://ebook.m2b.co.id/toolkit.html" 
                   style="display:inline-block;padding:14px 28px;background:linear-gradient(135deg,#f59e0b,#f97316);color:white;text-decoration:none;border-radius:8px;font-weight:700;font-size:15px;">
                    &#128295; Buka Toolkit Interaktif
                </a>
            </div>
            
            <!-- Support -->
            <h3 style="color:#333;">💬 Butuh Bantuan?</h3>
            <p style="color:#555;">Jika Anda memiliki pertanyaan atau membutuhkan konsultasi lebih lanjut:</p>
            <ul style="padding-left:20px;color:#555;">
                <li style="margin:4px 0;">📱 WhatsApp: <strong>+62 822-6184-6811</strong></li>
                <li style="margin:4px 0;">📧 Email: <strong>ebook@m2b.co.id</strong></li>
                <li style="margin:4px 0;">💬 Telegram: <a href="{$telegramGroup}" style="color:#667eea;">Join Group</a></li>
            </ul>
            
            <p style="margin-top:32px;">Selamat belajar dan semoga sukses dalam bisnis ekspor-impor Anda! 🚀</p>
            
            <p style="color:#666;font-size:14px;font-style:italic;">
                Terima kasih telah mempercayai M2B sebagai partner edukasi Anda.
            </p>
        </div>
        
        <!-- Footer -->
        <div style="text-align:center;padding:24px;color:#666;font-size:13px;">
            <p style="margin:4px 0;"><strong>M2B Global Trade Academy</strong></p>
            <p style="margin:4px 0;">📧 ebook@m2b.co.id | 📱 +62 822-6184-6811</p>
            <p style="margin:4px 0;">🌐 <a href="https://m2b.co.id" style="color:#667eea;">m2b.co.id</a></p>
            <p style="margin-top:16px;color:#999;">© 2026 M2B. Hak Cipta Dilindungi.</p>
        </div>
    </div>
</body>
</html>
HTML;
        
        return $html;
    }
    

    /**
     * Payment Rejection Email Template
     */
    private function getPaymentRejectionHTML($data) {
        $adminWa = defined('ADMIN_WHATSAPP') ? ADMIN_WHATSAPP : '6282261846811';
        $price = defined('EBOOK_PRICE') ? EBOOK_PRICE : 49000;
        $priceFormatted = 'Rp ' . number_format($price, 0, ',', '.');
        $bankName = defined('BANK_NAME') ? BANK_NAME : 'BCA';
        $bankAccount = defined('BANK_ACCOUNT') ? BANK_ACCOUNT : '8280424243';
        $bankHolder = defined('BANK_HOLDER') ? BANK_HOLDER : 'Eka Mayang Sari Harahap';
        
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Pembayaran - M2B</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;line-height:1.6;color:#333;background:#f5f5f5;">
    <div style="max-width:600px;margin:0 auto;padding:20px;">
        <!-- Header -->
        <div style="background:linear-gradient(135deg,#ef4444 0%,#dc2626 100%);color:white;padding:40px 30px;text-align:center;border-radius:16px 16px 0 0;">
            <h1 style="margin:0;font-size:28px;">⚠️ Informasi Pembayaran</h1>
        </div>
        
        <!-- Content -->
        <div style="background:white;padding:40px 30px;border-radius:0 0 16px 16px;">
            <!-- Alert Box -->
            <div style="background:#fef2f2;border:2px solid #ef4444;border-radius:12px;padding:24px;text-align:center;margin-bottom:24px;">
                <h2 style="color:#991b1b;margin:0;">Pembayaran Belum Dapat Diverifikasi</h2>
                <p style="margin:8px 0 0 0;color:#b91c1c;">Order ID: <strong>{$data['order_id']}</strong></p>
            </div>
            
            <p style="font-size:16px;">Halo <strong>{$data['buyer_name']}</strong>,</p>
            
            <p>Terima kasih atas minat Anda pada e-book <strong>"Panduan Global Export & Import untuk Indonesia"</strong>.</p>
            
            <p>Mohon maaf, kami belum dapat memverifikasi pembayaran untuk pesanan Anda. Ini bisa terjadi karena:</p>
            
            <ul style="padding-left:20px;color:#555;">
                <li style="margin:8px 0;">Pembayaran belum kami terima</li>
                <li style="margin:8px 0;">Jumlah transfer tidak sesuai ({$priceFormatted})</li>
                <li style="margin:8px 0;">Bukti pembayaran belum dikirim atau tidak jelas</li>
            </ul>
            
            <!-- Retry Payment Box -->
            <div style="background:#f0fdf4;border:2px solid #10b981;border-radius:12px;padding:24px;margin:24px 0;">
                <h3 style="margin:0 0 12px 0;color:#065f46;">💡 Cara Melanjutkan Pembayaran:</h3>
                <ol style="padding-left:20px;color:#555;">
                    <li style="margin:8px 0;">Transfer <strong>{$priceFormatted}</strong> ke <strong>{$bankName} {$bankAccount}</strong> a/n <strong>{$bankHolder}</strong></li>
                    <li style="margin:8px 0;">Screenshot bukti transfer</li>
                    <li style="margin:8px 0;">Kirim bukti via WhatsApp beserta Order ID: <strong>{$data['order_id']}</strong></li>
                </ol>
            </div>
            
            <!-- CTA Button -->
            <div style="text-align:center;margin:32px 0;">
                <a href="https://wa.me/{$adminWa}?text=Halo%2C%20saya%20ingin%20konfirmasi%20pembayaran%20untuk%20Order%20ID%3A%20{$data['order_id']}"
                   style="display:inline-block;padding:16px 32px;background:linear-gradient(135deg,#10b981 0%,#059669 100%);color:white;text-decoration:none;border-radius:8px;font-weight:600;font-size:16px;">
                    💬 Hubungi Kami via WhatsApp
                </a>
            </div>
            
            <p style="color:#666;font-size:14px;margin-top:32px;font-style:italic;">
                Jika Anda merasa ini adalah kesalahan, silakan hubungi kami segera. Kami siap membantu!
            </p>
        </div>
        
        <!-- Footer -->
        <div style="text-align:center;padding:24px;color:#666;font-size:13px;">
            <p style="margin:4px 0;"><strong>M2B Global Trade Academy</strong></p>
            <p style="margin:4px 0;">📧 ebook@m2b.co.id | 📱 +62 822-6184-6811</p>
            <p style="margin:4px 0;">🌐 <a href="https://m2b.co.id" style="color:#667eea;">m2b.co.id</a></p>
            <p style="margin-top:16px;color:#999;">© 2026 M2B. Hak Cipta Dilindungi.</p>
        </div>
    </div>
</body>
</html>
HTML;
        
        return $html;
    }

    /**
     * Payment Reminder Email Template
     */
    private function getPaymentReminderHTML($data) {
        $adminWa = defined('ADMIN_WHATSAPP') ? ADMIN_WHATSAPP : '6282261846811';
        $price = defined('EBOOK_PRICE') ? EBOOK_PRICE : 49000;
        $priceFormatted = 'Rp ' . number_format($price, 0, ',', '.');
        $bankName = defined('BANK_NAME') ? BANK_NAME : 'BCA';
        $bankAccount = defined('BANK_ACCOUNT') ? BANK_ACCOUNT : '8280424243';
        $bankHolder = defined('BANK_HOLDER') ? BANK_HOLDER : 'Eka Mayang Sari Harahap';
        
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Pembayaran - M2B</title>
</head>
<body style="margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;line-height:1.6;color:#333;background:#f5f5f5;">
    <div style="max-width:600px;margin:0 auto;padding:20px;">
        <!-- Header -->
        <div style="background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);color:white;padding:40px 30px;text-align:center;border-radius:16px 16px 0 0;">
            <h1 style="margin:0;font-size:28px;">⏰ Jangan Lewatkan E-book Anda!</h1>
        </div>
        
        <!-- Content -->
        <div style="background:white;padding:40px 30px;border-radius:0 0 16px 16px;">
            <p style="font-size:16px;">Halo <strong>{$data['buyer_name']}</strong>,</p>
            
            <p>Kami melihat bahwa pesanan e-book Anda belum selesai diproses. Buku <strong>"Panduan Global Export & Import untuk Indonesia v2.0"</strong> masih menunggu Anda! 📚</p>
            
            <!-- Order Reminder Box -->
            <div style="background:#fffbeb;border:2px solid #f59e0b;border-radius:12px;padding:24px;margin:24px 0;">
                <h3 style="margin:0 0 16px 0;color:#92400e;">📋 Pesanan Anda</h3>
                <table style="width:100%;border-collapse:collapse;">
                    <tr>
                        <td style="padding:8px 0;color:#666;">Order ID:</td>
                        <td style="padding:8px 0;text-align:right;font-weight:600;">{$data['order_id']}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#666;">Nama:</td>
                        <td style="padding:8px 0;text-align:right;">{$data['buyer_name']}</td>
                    </tr>
                    <tr style="border-top:1px dashed #ddd;">
                        <td style="padding:12px 0 0 0;color:#666;font-weight:600;">Harga:</td>
                        <td style="padding:12px 0 0 0;text-align:right;font-size:24px;font-weight:700;color:#d97706;">{$priceFormatted}</td>
                    </tr>
                </table>
            </div>
            
            <!-- What You'll Get -->
            <h3 style="color:#333;">🎯 Yang Akan Anda Dapatkan:</h3>
            <ul style="padding-left:20px;color:#555;">
                <li style="margin:8px 0;">📖 22 Chapter komprehensif (v2.0 terbaru)</li>
                <li style="margin:8px 0;">🌍 Panduan dari mindset sampai praktik ekspor-impor</li>
                <li style="margin:8px 0;">📄 Template dokumen siap pakai</li>
                <li style="margin:8px 0;">💬 Akses ke komunitas Telegram eksklusif</li>
                <li style="margin:8px 0;">🇮🇩🇬🇧 Tersedia dalam Bahasa Indonesia & English</li>
            </ul>
            
            <!-- Payment Info -->
            <div style="background:#f0fdf4;border:2px solid #10b981;border-radius:12px;padding:24px;margin:24px 0;">
                <h3 style="margin:0 0 12px 0;color:#065f46;">💳 Transfer ke:</h3>
                <p style="margin:4px 0;"><strong>Bank:</strong> {$bankName}</p>
                <p style="margin:4px 0;"><strong>No. Rekening:</strong> <span style="font-size:20px;font-family:monospace;color:#000;">{$bankAccount}</span></p>
                <p style="margin:4px 0;"><strong>Atas Nama:</strong> {$bankHolder}</p>
                <p style="margin:12px 0 0 0;"><strong>Jumlah:</strong> <span style="font-size:20px;color:#dc2626;font-weight:700;">{$priceFormatted}</span></p>
            </div>
            
            <!-- CTA Button -->
            <div style="text-align:center;margin:32px 0;">
                <a href="https://wa.me/{$adminWa}?text=Halo%2C%20saya%20ingin%20melanjutkan%20pembayaran%20untuk%20Order%20ID%3A%20{$data['order_id']}"
                   style="display:inline-block;padding:16px 32px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;text-decoration:none;border-radius:8px;font-weight:600;font-size:16px;">
                    💬 Konfirmasi Pembayaran via WhatsApp
                </a>
            </div>
            
            <p style="color:#999;font-size:13px;text-align:center;margin-top:24px;">
                Jika Anda sudah melakukan pembayaran, abaikan email ini.<br>
                Tim kami akan segera memverifikasi pembayaran Anda.
            </p>
        </div>
        
        <!-- Footer -->
        <div style="text-align:center;padding:24px;color:#666;font-size:13px;">
            <p style="margin:4px 0;"><strong>M2B Global Trade Academy</strong></p>
            <p style="margin:4px 0;">📧 ebook@m2b.co.id | 📱 +62 822-6184-6811</p>
            <p style="margin:4px 0;">🌐 <a href="https://m2b.co.id" style="color:#667eea;">m2b.co.id</a></p>
            <p style="margin-top:16px;color:#999;">© 2026 M2B. Hak Cipta Dilindungi.</p>
        </div>
    </div>
</body>
</html>
HTML;
        
        return $html;
    }

    /**
     * Send email using PHP mail()
     */
    private function sendEmail($to, $subject, $htmlMessage) {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            "From: {$this->from_name} <{$this->from_email}>",
            "Reply-To: {$this->reply_to}",
            'X-Mailer: PHP/' . phpversion()
        ];
        
        $success = @mail($to, $subject, $htmlMessage, implode("\r\n", $headers));
        
        if ($success) {
            error_log("Email sent successfully to: $to - Subject: $subject");
        } else {
            error_log("Failed to send email to: $to - Subject: $subject");
        }
        
        return $success;
    }
}
