# Pipeline Master E-book (v2.1+)

Sumber master: `ebook-id.html` (HTML print-ready, CSS paged media).
Folder ini DIBLOKIR dari akses publik via .htaccess.

## Regenerate PDF
```bash
# 1. Render (butuh: brew install weasyprint)
weasyprint print/ebook-id.html /tmp/ebook-v2x-id.pdf

# 2. WAJIB: konversi ke PDF 1.4 agar kompatibel FPDI free parser (watermark_pdf.php)
#    (butuh: brew install ghostscript)
gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 \
   -dPDFSETTINGS=/prepress -sOutputFile=/tmp/ebook-master-id.pdf /tmp/ebook-v2x-id.pdf

# 3. Upload ke server menggantikan ~/domains/m2b.co.id/public_html/ebook/ebook-master-id.pdf
#    (backup dulu yang lama; MASTER_EBOOK_PATH di config.php menunjuk ke file ini)
```

QR code di `assets/` dibuat dengan python qrcode (lihat riwayat sesi 2026-07-11).
Setiap naik versi: perbarui halaman "Riwayat Versi" di ebook-id.html + badge versi cover.
