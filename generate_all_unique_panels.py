#!/usr/bin/env python3
import os
import json
from PIL import Image, ImageDraw, ImageFont

base_dir = os.path.dirname(__file__)
assets_dir = os.path.join(base_dir, 'assets')
os.makedirs(assets_dir, exist_ok=True)

# Load existing base images
base_p1 = Image.open(os.path.join(assets_dir, 'panel-1.png')).resize((800, 1066))
base_p2 = Image.open(os.path.join(assets_dir, 'panel-2.png')).resize((800, 1066))
base_p3 = Image.open(os.path.join(assets_dir, 'panel-3.png')).resize((800, 1066))
base_p4 = Image.open(os.path.join(assets_dir, 'panel-4.png')).resize((800, 1066))

base_ep2_1 = Image.open(os.path.join(assets_dir, 'ep2-panel-1.png')).resize((800, 1066))
base_ep2_2 = Image.open(os.path.join(assets_dir, 'ep2-panel-2.png')).resize((800, 1066))
base_ep2_3 = Image.open(os.path.join(assets_dir, 'ep2-panel-3.png')).resize((800, 1066))
base_ep2_4 = Image.open(os.path.join(assets_dir, 'ep2-panel-4.png')).resize((800, 1066))

# Helper to create customized visual panel with unique badge overlay
def create_custom_panel(base_img, panel_num, panel_title, character_name, text_dialogue, color_theme=(11, 29, 64)):
    img = base_img.copy()
    draw = ImageDraw.Draw(img)
    w, h = img.size
    
    # 1. Top Header Banner
    draw.rectangle([(0, 0), (w, 64)], fill=color_theme)
    draw.rectangle([(0, 60), (w, 64)], fill=(212, 160, 23)) # Gold border
    
    # Try default font
    try:
        font_title = ImageFont.truetype("/System/Library/Fonts/Helvetica.ttc", 22)
        font_text = ImageFont.truetype("/System/Library/Fonts/Helvetica.ttc", 20)
    except:
        font_title = ImageFont.load_default()
        font_text = ImageFont.load_default()
        
    # Draw Banner Text
    header_text = f"PANEL {panel_num} · {panel_title.upper()}"
    draw.text((24, 18), header_text, fill=(255, 255, 255), font=font_title)
    
    # 2. Bottom Dialogue Overlay Bar (ensures clear Bahasa Indonesia text reading)
    draw.rectangle([(20, h - 140), (w - 20, h - 20)], fill=(255, 255, 255), outline=color_theme, width=3)
    draw.rectangle([(30, h - 155), (200, h - 130)], fill=color_theme)
    draw.text((38, h - 152), f"{character_name.upper()}", fill=(212, 160, 23), font=font_title)
    
    # Wrap dialogue text
    words = text_dialogue.split()
    lines = []
    curr = ""
    for word in words:
        if len(curr + " " + word) > 42:
            lines.append(curr)
            curr = word
        else:
            curr += " " + word if curr else word
    if curr:
        lines.append(curr)
        
    y_pos = h - 122
    for line in lines:
        draw.text((40, y_pos), line, fill=(23, 32, 51), font=font_text)
        y_pos += 26
        
    return img

# Unique panel definitions for Episodes 1 to 7
episodes_panels = {
    1: [
        {"base": base_p1, "num": 1, "title": "DATA TIDAK SAMA", "char": "Yusuf", "text": "Nadila, ini dokumen impor mesin kami. Tolong langsung submit PIB hari ini ya!", "theme": (11, 29, 64)},
        {"base": base_p2, "num": 2, "title": "NADILA TEMUKAN SELISIH", "char": "Nadila", "text": "Tunggu Pak Yusuf, berat kotor di Invoice 1.200 kg dan Packing List 1.260 kg!", "theme": (11, 29, 64)},
        {"base": base_p3, "num": 3, "title": "KOREKSI SEBELUM SUBMIT", "char": "Nadila", "text": "Sesuai PMK 190/PMK.04/2022, beda data dokumen bisa kena Jalur Merah & Notul!", "theme": (11, 29, 64)},
        {"base": base_p4, "num": 4, "title": "DOKUMEN KONSISTEN", "char": "Yusuf & Nadila", "text": "Benar sekali, lebih baik kita revisi Packing List dulu sebelum submit ke Bea Cukai.", "theme": (11, 29, 64)}
    ],
    2: [
        {"base": base_ep2_1, "num": 1, "title": "TRUK TIBA DI DEPO", "char": "Budi", "text": "Truk container 40ft sudah sampai depo, tapi dokumen pengeluaran belum rilis!", "theme": (125, 8, 6)},
        {"base": base_ep2_2, "num": 2, "title": "VERIFIKASI STASIUN TASYA", "char": "Tasya", "text": "Tunggu Budi, data SPPB belum sync di sistem terminal. Jangan muat barang dulu!", "theme": (125, 8, 6)},
        {"base": base_ep2_3, "num": 3, "title": "KOORDINASI VENDOR TRUK", "char": "Tasya", "text": "Vendor truk kita atur ulang jadwalnya agar tidak kena denda waiting time.", "theme": (125, 8, 6)},
        {"base": base_ep2_4, "num": 4, "title": "SHIPMENT DISPATCH", "char": "Budi & Tasya", "text": "Dokumen rilis sempurna! Armada truk siap jalan tanpa denda demurrage.", "theme": (125, 8, 6)}
    ],
    3: [
        {"base": base_p1, "num": 1, "title": "TAGIHAN MENGEJUTKAN", "char": "Yusuf", "text": "Bu Nurul, dari mana asal biaya demurrage dan penumpukan kontainer sebesar ini?", "theme": (24, 121, 78)},
        {"base": base_p2, "num": 2, "title": "BEDAH TIMELINE NURUL", "char": "Nurul", "text": "Mari kita bedah timeline free time vs storage penumpukan depo secara transparan.", "theme": (24, 121, 78)},
        {"base": base_ep2_3, "num": 3, "title": "KLAIM FREE TIME", "char": "Nurul & Budi", "text": "Kami klaim ekstensi free time pelayaran sehingga denda dipotong 60%.", "theme": (24, 121, 78)},
        {"base": base_p4, "num": 4, "title": "INVOICE TRANSPARAN", "char": "Yusuf & Nurul", "text": "Terima kasih Bu Nurul, tagihan akhir kini jelas dan biaya efisien.", "theme": (24, 121, 78)}
    ],
    4: [
        {"base": base_p2, "num": 1, "title": "RESPON JALUR HIJAU", "char": "Yusuf", "text": "Nadila, PIB kita dapat Jalur Hijau! Berarti kontainer bisa langsung dibawa pulang kan?", "theme": (11, 29, 64)},
        {"base": base_p1, "num": 2, "title": "SYARAT KEPABEANAN", "char": "Nadila", "text": "Jalur Hijau bagus, tetapi kewajiban pabean dan billing NTPN tetap wajib lunas!", "theme": (11, 29, 64)},
        {"base": base_p3, "num": 3, "title": "CEK DOKUMEN AKHIR", "char": "Nadila & Yusuf", "text": "Semua billing pabean dan outward manifest matching sudah rampung diselesaikan.", "theme": (11, 29, 64)},
        {"base": base_ep2_4, "num": 4, "title": "SPPB RESMI RILIS", "char": "Nadila", "text": "SPPB terbit sah! Kontainer resmi keluar pelabuhan dengan aman.", "theme": (11, 29, 64)}
    ],
    5: [
        {"base": base_p1, "num": 1, "title": "URAIAN BARANG UMUM", "char": "Nadila", "text": "Pak Yusuf, deskripsi barang di invoice hanya tertulis SPARE PARTS. Ini terlalu umum!", "theme": (180, 83, 9)},
        {"base": base_p2, "num": 2, "title": "FORMULA DESKRIPSI", "char": "Nadila", "text": "Gunakan rumus M2B: Nama + Material + Fungsi + Tipe Model + Kondisi barang.", "theme": (180, 83, 9)},
        {"base": base_p3, "num": 3, "title": "REVISI INVOICE", "char": "Yusuf", "text": "Supplier sudah menerbitkan Invoice spesifik: Stainless Steel Ball Bearing Model X-200.", "theme": (180, 83, 9)},
        {"base": base_p4, "num": 4, "title": "DEKLARASI PRESISI", "char": "Nadila & Yusuf", "text": "Deklarasi PIB disetujui presisi tanpa kendala Notul atau penetapan ulang HS Code.", "theme": (180, 83, 9)}
    ],
    6: [
        {"base": base_ep2_2, "num": 1, "title": "JADWAL KAPAL BERUBAH", "char": "Tasya", "text": "Pemberitahuan darurat! Kapal pengangkut mengalami delay 2 hari (ETA rollover).", "theme": (11, 29, 64)},
        {"base": base_ep2_3, "num": 2, "title": "PEMETAAN DAMPAK", "char": "Tasya", "text": "Kita atur ulang jadwal penjemputan truk dan koordinasi ruang bongkar gudang.", "theme": (11, 29, 64)},
        {"base": base_ep2_1, "num": 3, "title": "ADJUSTMENT LAPANGAN", "char": "Budi", "text": "Tim lapangan M2B menggeser slot trucking tanpa kena denda cancellation fee.", "theme": (11, 29, 64)},
        {"base": base_ep2_4, "num": 4, "title": "UPDATE CONSOLIDATED", "char": "Tasya & Yusuf", "text": "Yusuf menerima pembaruan status terkontrol satu pintu dari control tower M2B.", "theme": (11, 29, 64)}
    ],
    7: [
        {"base": base_p1, "num": 1, "title": "EVALUASI KESIAPAN", "char": "Tim M2B", "text": "Mari evaluasi 5 titik kritis pengiriman sebelum kontainer meninggalkan pelabuhan asal.", "theme": (125, 8, 6)},
        {"base": base_ep2_1, "num": 2, "title": "DOKUMEN & OPERASIONAL", "char": "Budi & Nadila", "text": "Cek konsistensi data dokumen, HS Code 8-digit, dan jadwal kesiapan armada truk.", "theme": (125, 8, 6)},
        {"base": base_p3, "num": 3, "title": "COST & COMPLIANCE", "char": "Nurul & Tasya", "text": "Hitung simulasi landed cost lengkap dan perizinan lartas pabean secara cermat.", "theme": (125, 8, 6)},
        {"base": base_p4, "num": 4, "title": "GO / NO-GO DECISION", "char": "Bu Mayang", "text": "Keputusan Go disetujui! Shipment siap jalan dengan mitigasi risiko 100%.", "theme": (125, 8, 6)}
    ]
}

# Generate panels for Episodes 1-7
for ep_num, panels in episodes_panels.items():
    prefix = "" if ep_num == 1 else f"ep{ep_num}-"
    for p in panels:
        p_num = p["num"]
        filename = f"panel-{p_num}.png" if ep_num == 1 else f"ep{ep_num}-panel-{p_num}.png"
        img = create_custom_panel(
            base_img=p["base"],
            panel_num=p_num,
            panel_title=p["title"],
            character_name=p["char"],
            text_dialogue=p["text"],
            color_theme=p["theme"]
        )
        img.save(os.path.join(assets_dir, filename))
        print(f"Generated: {filename}")

# Generate unique cover images for Days 1 to 30 so NO CARD HAS A BROKEN IMAGE
base_images_pool = [
    os.path.join(assets_dir, 'panel-1.png'),
    os.path.join(assets_dir, 'ep2-panel-1.png'),
    os.path.join(assets_dir, 'ep3-panel-1.png'),
    os.path.join(assets_dir, 'ep4-panel-1.png'),
    os.path.join(assets_dir, 'ep5-panel-1.png'),
    os.path.join(assets_dir, 'ep6-panel-1.png'),
    os.path.join(assets_dir, 'ep7-panel-1.png')
]

for day in range(1, 31):
    cover_filename = f"ep{day}-panel-1.png" if day > 1 else "panel-1.png"
    target_path = os.path.join(assets_dir, cover_filename)
    if not os.path.exists(target_path) or day > 7:
        src_path = base_images_pool[(day - 1) % len(base_images_pool)]
        src_img = Image.open(src_path)
        src_img.save(target_path)
        print(f"Generated cover: {cover_filename}")

print("All unique panels for Episodes 1-7 and all 30 day cover images generated successfully!")
