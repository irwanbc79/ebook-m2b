#!/usr/bin/env python3
import os
import json

base_dir = os.path.dirname(__file__)
stories_path = os.path.join(base_dir, 'stories.html')
json_path = os.path.join(base_dir, 'content_plan_30_hari.json')

with open(json_path, 'r', encoding='utf-8') as f:
    plan_data = json.load(f)

# Mapping of Published Episodes & Panel 1 cover images
published_eps = {
    1: {"slug": "episode-01.html", "img": "assets/panel-1.png"},
    2: {"slug": "episode-02.html", "img": "assets/ep2-panel-1.png"},
    3: {"slug": "episode-03.html", "img": "assets/ep3-panel-1.png"},
    4: {"slug": "episode-04.html", "img": "assets/ep4-panel-1.png"},
    5: {"slug": "episode-05.html", "img": "assets/ep5-panel-1.png"},
    6: {"slug": "episode-06.html", "img": "assets/ep6-panel-1.png"},
    7: {"slug": "episode-07.html", "img": "assets/ep7-panel-1.png"},
}

grid_cards_html = []

for ep in plan_data:
    day = ep["day"]
    title = ep["title"]
    lead = ep["lead_character"]
    category = ep["category"]
    case_desc = ep["case"]
    
    if day in published_eps:
        slug = published_eps[day]["slug"]
        cover_img = published_eps[day]["img"]
        status_class = "published"
        badge_html = '<span class="ep-badge published">Terbit ✓</span>'
        link_html = f'<a href="{slug}" class="ep-link">Baca &amp; Lihat Komik →</a>'
    else:
        cover_img = f"assets/ep{((day - 1) % 7) + 1}-panel-1.png"
        status_class = "upcoming"
        badge_html = '<span class="ep-badge upcoming">Segera Terbit</span>'
        link_html = f'<span class="ep-link" style="color:var(--muted);">Segera Terbit</span>'

    card_code = f"""
        <!-- Episode {day:02d} -->
        <article class="ep-card {status_class}">
          <div class="ep-cover">
            <img src="{cover_img}" alt="Adegan 1: {title}" width="400" height="250" loading="lazy">
          </div>
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

# Replace <div class="episodes-grid"> ... </div>
import re
pattern = r'(<div class="episodes-grid">)(.*?)(</div>\s*</section>)'
replacement = r'\1\n' + new_grid_content + r'\n      \3'

updated_html = re.sub(pattern, replacement, stories_html, flags=re.DOTALL)

with open(stories_path, 'w', encoding='utf-8') as f:
    f.write(updated_html)

print("Updated stories.html grid with Adegan Pertama cover images!")
