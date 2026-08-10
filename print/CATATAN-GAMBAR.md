# Catatan Audit Comic Strip (audit 2026-08-10)

Semua teks pada 8 gambar comic strip adalah **raster** (dibakar ke dalam gambar oleh
ImageGen), bukan teks vektor. Konsekuensinya: tidak bisa di-select/search di PDF, tidak
terbaca screen reader, nol nilai SEO — karena itu setiap `<img>` wajib punya `alt`
deskriptif (sudah dipasang di `index.html` dan sumber PDF).

Perbaikan teks hanya mungkin dilakukan di area **warna rata** (band caption). Teks yang
tergambar di dalam ilustrasi (layar tablet, monitor, bagan) **tidak bisa diperbaiki**
tanpa membuat ulang gambar.

## Status: SELESAI (2026-08-10)

Semua temuan sudah diperbaiki dan diverifikasi. Sumber gambar final ada di
`~/.codex/.chatgpt-projects/g-p-6934b2e10bac8191b7c737588ab32c7d/assets/comic-strip-m2b/`.

| Gambar | Perbaikan | Oleh |
|---|---|---|
| `part-2-operations-en` | `READY FOR THE RED CHANNEL` → `READY FOR RED-LANE INSPECTION` | cat-ulang band (skrip) |
| `part-1-fundamentals-en` | Seluruh teks tablet & monitor → Inggris (`Product Specification`, `GREEN CHANNEL`, `CLEARED`, dst) | regenerate |
| `part-4-operational-deep-dive-en` | `SELLER`, `BUYER`, `COST BORNE BY`, `RISK TRANSFERS AT`, `PROSPECTS & OUTREACH` | regenerate |
| `bagian-1-fundamental-id` | `CHANNEL HIJAU` → `JALUR HIJAU` | regenerate |
| `bagian-3-scale-future-id` | `ASIA SELATAN` → `ASIA TENGGARA`; `FOUNDATION/EXPANSION/SCALE` → `FONDASI/EKSPANSI/SKALA` | regenerate |

⚠️ **Perbaikan caption `part-2-operations-en` bersifat pasca-proses, bukan hasil ImageGen.**
Kalau gambar itu diregenerate lagi, perbaikannya HILANG. Skrip cat-ulangnya ada di
riwayat sesi 2026-08-10 (band x 842–1235, y 818–937, DIN Condensed Bold 34px, teks
di-center pada y-cap 858). Cara aman: minta ImageGen langsung menulis
`READY FOR RED-LANE INSPECTION`.

### Sisa nitpick (belum diperbaiki, prioritas rendah)

`bagian-3-scale-future-id` — chip bendera di baris `ASIA TENGGARA` masih memakai bendera
India (sisa dari label lama `ASIA SELATAN`). Ukurannya sangat kecil di halaman dan terbaca
sebagai ikon generik, jadi tidak menghambat rilis. Perbaiki bila kebetulan regenerate lagi.

## Riwayat temuan (arsip)

Alasan: frasa lama ambigu di telinga pembaca asing — terbaca seolah *menargetkan* jalur
merah. Versi ID sudah benar (`SIAP HADAPI JALUR MERAH`). Band dicat ulang dengan gradien
merah asli lalu teks digambar ulang memakai DIN Condensed Bold 34px agar seragam dengan
panel tetangga.

## Perlu regenerate (tidak bisa diperbaiki dari file jadi)

### 1. `m2b-ebook-part-1-fundamentals-en.png` — edisi EN masih berbahasa Indonesia
Teks Indonesia yang tertinggal di dalam ilustrasi:
- Panel 2 (tablet): `Spesifikasi Produk`, `Nama Produk`, `Bahan`, `Fungsi`, `Negara Asal`,
  `Deskripsi`, `Lentera Dekorasi Rotan`, `Anyaman rotan`
- Panel 4 (monitor): `CHANNEL HIJAU`, `CLEARANCE LANCAR`

Terjemahan yang benar: `Product Specification`, `Product Name`, `Material`, `Function`,
`Country of Origin`, `Description`, `Rattan Decorative Lantern`, `Woven rattan`,
`GREEN CHANNEL`, `CLEARED`.

### 2. `m2b-ebook-part-4-operational-deep-dive-en.png` — idem
- Panel 1 (bagan Incoterms): `PENJUAL`, `PEMBELI`, `BIAYA DITANGGUNG`, `RISIKO BERPINDAH`
- Panel 4 (dashboard): `PROSPEK & OUTREACH`

Terjemahan: `SELLER`, `BUYER`, `COST BORNE BY`, `RISK TRANSFERS AT`, `PROSPECTS & OUTREACH`.

### 3. `m2b-ebook-bagian-1-fundamental-id.png` — istilah keliru
Monitor panel 4 tertulis `CHANNEL HIJAU` (campur Inggris-Indonesia).
Istilah baku Bea Cukai Indonesia: **`JALUR HIJAU`**.

### 4. `m2b-ebook-bagian-3-scale-future-id.png` — istilah keliru + campur bahasa
- Daftar pasar tertulis `ASIA SELATAN`, padahal versi EN `SOUTHEAST ASIA`.
  Yang benar untuk ID: **`ASIA TENGGARA`**.
- Panel 4 campur bahasa: `TAHUN 1 FOUNDATION`, `TAHUN 2 EXPANSION`, `TAHUN 3 SCALE`
  → sebaiknya `TAHUN 1 FONDASI`, `TAHUN 2 EKSPANSI`, `TAHUN 3 SKALA`.

## Cara regenerate

Gunakan ImageGen yang sama (prompt asli ada di riwayat sesi 2026-08-10, ~10.21–10.42),
tambahkan instruksi eksplisit:

> Pertahankan komposisi, gaya ilustrasi, palet (navy #0b1d40, emas #d4a017, merah #7d0806),
> rasio 1672×941, dan tata letak 4 panel + band caption persis seperti gambar sebelumnya.
> Ubah HANYA teks berikut: `<daftar teks lama → teks baru>`.
> Untuk edisi EN, SELURUH teks di dalam ilustrasi harus berbahasa Inggris — termasuk teks
> pada layar tablet, monitor, dokumen, dan bagan, bukan hanya judul dan caption.

Setelah dapat PNG baru, jalankan ulang optimasi (lebar 1400 px):

```bash
python3 - <<'PY'
from PIL import Image
src="m2b-ebook-bagian-1-fundamental-id.png"   # ganti sesuai file
im=Image.open(src).convert("RGB")
im=im.resize((1400, round(im.height*1400/im.width)), Image.LANCZOS)
im.save("print/assets/comic-bagian-1-id.jpg","JPEG",quality=84,optimize=True)   # untuk PDF
im.resize((1400,788)).save("img/comic-bagian-1.webp","WEBP",quality=80,method=6) # untuk web
PY
```

Nama file sengaja dibuat stabil, jadi mengganti gambar = timpa file + regenerate PDF.
Tidak perlu menyentuh HTML.

## Catatan akurasi konten

Bagan Incoterms di panel 1 Bagian IV bersifat **dekoratif**, bukan rujukan: hanya
menampilkan 7 dari 11 terms Incoterms 2020 (FAS, CPT, CIP, DPU tidak ada) dan titik
perpindahan biaya/risiko tidak terbaca presisi pada ukuran tampil. Rujukan yang sahih
tetap tabel Incoterms di Bab 17. Jangan promosikan gambar ini sebagai ringkasan Incoterms.
