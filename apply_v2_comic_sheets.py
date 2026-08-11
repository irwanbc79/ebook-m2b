#!/usr/bin/env python3
import os
import json
import re

base_dir = os.path.dirname(__file__)

# Mapping of v2 composite comic sheets for Episodes 01-07
sheets_map = {
    1: {
        "file": "assets/stories-v2/m2b-story-episode-01.png",
        "title": "Invoice, Packing List, dan B/L Tidak Sama",
        "alt": "M2B Logistics Stories Episode 01 — Invoice, Packing List, dan B/L Tidak Sama"
    },
    2: {
        "file": "assets/stories-v2/m2b-story-episode-02.png",
        "title": "Truk Sudah Tiba, Dokumen Belum Siap",
        "alt": "M2B Logistics Stories Episode 02 — Truk sudah tiba tetapi dokumen release belum siap"
    },
    3: {
        "file": "assets/stories-v2/m2b-story-episode-03.png",
        "title": "Dari Mana Biaya Demurrage Ini?",
        "alt": "M2B Logistics Stories Episode 03 — Membedah biaya demurrage, detention, dan penumpukan"
    },
    4: {
        "file": "assets/stories-v2/m2b-story-episode-04.png",
        "title": "Jalur Hijau Bukan Berarti Langsung Keluar",
        "alt": "M2B Logistics Stories Episode 04 — Prosedur kepabeanan dan rilis barang jalur hijau"
    },
    5: {
        "file": "assets/stories-v2/m2b-story-episode-05.png",
        "title": "Deskripsi Produk Terlalu Umum",
        "alt": "M2B Logistics Stories Episode 05 — Rumus 5 elemen deskripsi barang commercial invoice"
    },
    6: {
        "file": "assets/stories-v2/m2b-story-episode-06.png",
        "title": "Jadwal Kapal Berubah Mendadak",
        "alt": "M2B Logistics Stories Episode 06 — Penanganan krisis keterlambatan kapal dan reschedule truk"
    },
    7: {
        "file": "assets/stories-v2/m2b-story-episode-07.png",
        "title": "5 Tanda Bahaya Sebelum Shipment Bergerak",
        "alt": "M2B Logistics Stories Episode 07 — Matriks kesiapan shipment dan evaluasi go/no-go"
    }
}

stories_path = os.path.join(base_dir, 'stories.html')
json_path = os.path.join(base_dir, 'content_plan_30_hari.json')

with open(json_path, 'r', encoding='utf-8') as f:
    plan_data = json.load(f)

grid_cards_html = []

for ep in plan_data:
    day = ep["day"]
    
    if day in sheets_map:
        slug = f"episode-{day:02d}.html"
        cover_img = sheets_map[day]["file"]
        title = sheets_map[day]["title"]
        lead = ep["lead_character"]
        category = ep["category"]
        case_desc = ep["case"]
        status_class = "published"
        badge_html = '<span class="ep-badge published">Terbit ✓</span>'
        cover_html = f'''
          <div class="ep-cover story-cover">
            <img src="{cover_img}" alt="M2B Episode {day:02d}: {title}" width="400" height="225" loading="lazy">
          </div>'''
        link_html = f'<a href="{slug}" class="ep-link">Baca &amp; Lihat Komik →</a>'
    else:
        title = ep["title"]
        lead = ep["lead_character"]
        category = ep["category"]
        case_desc = ep["case"]
        status_class = "upcoming"
        badge_html = '<span class="ep-badge upcoming">Segera Terbit</span>'
        cover_html = f'''
          <div class="ep-cover upcoming-cover">
            <div class="upcoming-inner">
              <span class="ep-day-tag">HARI {day:02d} · {category.upper()}</span>
              <b>🔒 Komik &amp; Artikel Segera Terbit</b>
              <span>Jadwal Rilis Konten M2B</span>
            </div>
          </div>'''
        link_html = f'<span class="ep-link" style="color:var(--muted);">Segera Terbit</span>'

    card_code = f"""
        <!-- Episode {day:02d} -->
        <article class="ep-card {status_class}">
          {cover_html}
          <div class="ep-card-body">
            <div class="ep-header">
              <span class="ep-num">Hari {day:02d} · {category}</span>
              {badge_html}
            </div>
            <h3>{title}</h3>
            <p>{case_desc}</p>
            <div class="ep-meta">
              <span>Lead: <strong>{lead}</strong></span>
              {link_html}
            </div>
          </div>
        </article>"""
    grid_cards_html.append(card_code)

new_grid_content = "\n".join(grid_cards_html)

with open(stories_path, 'r', encoding='utf-8') as f:
    stories_html = f.read()

pattern = r'(<div class="episodes-grid">)(.*?)(</div>\s*</section>)'
replacement = r'\1\n' + new_grid_content + r'\n      \3'

updated_html = re.sub(pattern, replacement, stories_html, flags=re.DOTALL)

with open(stories_path, 'w', encoding='utf-8') as f:
    f.write(updated_html)

print("Updated stories.html: Episodes 01-07 now use composite 4-panel sheet covers!")
