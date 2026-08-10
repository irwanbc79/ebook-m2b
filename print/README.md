# Pipeline Master E-book (v2.1+)

Sumber master: `ebook-id.html` / `ebook-en.html` (HTML print-ready, CSS paged media).
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

Ulangi langkah yang sama untuk `ebook-en.html` → `ebook-master-en.pdf`.

## Aset gambar (`assets/`)

| File | Dipakai di |
|---|---|
| `cover-artwork.jpg` | latar halaman sampul (`.bleed`, `background-size: cover`) |
| `comic-bagian-{1..4}-id.jpg` | halaman pembuka Bagian I–IV, edisi ID |
| `comic-part-{1..4}-en.jpg` | halaman pembuka Part I–IV, edisi EN |
| `qr-toolkit.png`, `qr-wa.png` | halaman QR |

Comic strip dipasang **full-bleed di atas** halaman Bagian (`.part-strip`, lebar 210mm).
Teks judul Bagian sengaja TIDAK diulang di bawah strip karena strip sudah memuatnya —
halaman hanya berisi tag, deskripsi, dan daftar bab.

Sumber PNG asli comic strip beresolusi penuh (~2,4 MB/file) **jangan** dipakai langsung;
selalu turunkan ke lebar 1400 px + JPEG q84 agar PDF tetap ≤ 2 MB. Pada 210mm,
1400 px = ±169 dpi — cukup tajam untuk baca layar maupun cetak rumahan.

## Batasan ukuran & watermark

PDF hasil akhir **harus ≤ 2 MB**. `watermark_pdf.php` (FPDI free parser) jalan di
Hostinger shared setiap kali pembeli mengunduh. Diuji 2026-08-10 pada master v2.2:
ID 1.441.000 byte / 37 hal dan EN 1.437.187 byte / 38 hal → watermark sukses dalam
0,04 detik, peak memory 6 MB. Aman dengan margin lebar.

### ⚠️ JANGAN ganti `-dPDFSETTINGS=/prepress`

Diuji dan GAGAL 2026-08-10: `/ebook` maupun `/printer` (dengan downsample manual)
memang menghasilkan file lebih kecil (1,2 MB), tapi **menghapus latar navy halaman
Bagian** — background gradien CSS hilang, halaman jadi putih dan teks isi nyaris tak
terbaca. Selisihnya 59% piksel dibanding `/prepress`. Comic strip dan header tetap
tampil, jadi kerusakan ini **mudah terlewat** kalau hanya melihat thumbnail.

Cara mengecilkan file yang benar: turunkan kualitas JPEG aset di `assets/`
(q84 → q79 memangkas ±150 KB tanpa perbedaan yang terlihat), **bukan** mengubah preset gs.

Setelah setiap build, verifikasi latar belum hilang:
```bash
gs -q -dNOPAUSE -dBATCH -sDEVICE=png16m -r58 -dFirstPage=5 -dLastPage=5 \
   -sOutputFile=/tmp/cek.png ebook-master-id.pdf
python3 -c "from PIL import Image; im=Image.open('/tmp/cek.png').convert('RGB'); w,h=im.size; \
print('latar:', im.getpixel((int(w*.05),int(h*.75))))"
# harus navy ±(12,31,67) — kalau (255,255,255) berarti background hilang
```

Uji ulang setiap kali menambah gambar:
```bash
composer install                      # vendor/ gitignored, aman
php -d memory_limit=128M -r '
  require "watermark_pdf.php";
  $w = new PDFWatermark();
  var_dump($w->addWatermark("master.pdf","out.pdf","Tes","tes@contoh.id"));
  printf("peak %.1f MB\n", memory_get_peak_usage(true)/1048576);'
```

## Catatan

- **Font Lora tidak terpasang** di mesin build — semua `font-family: Lora` jatuh ke
  Georgia. Pasang Lora bila ingin sesuai desain asli.
- Cover memakai teks HTML/CSS di atas foto, **bukan** gambar cover jadi. Ini disengaja:
  teks tetap vektor (tajam, bisa di-select/search) dan tidak terpotong oleh beda rasio
  (cover web 1:1,6 vs A4 1:1,41). Untuk web tetap pakai `img/ebook-cover-v2.*`.
- `cover-artwork.jpg` beresolusi 992 px (±120 dpi pada A4). Cukup untuk baca layar;
  bila perlu kualitas cetak, ekspor ulang artwork dari `cover-source.svg` lebih besar.
- QR code di `assets/` dibuat dengan python qrcode (lihat riwayat sesi 2026-07-11).
- Setiap naik versi: perbarui halaman "Riwayat Versi" + badge versi di landing page.
