#!/usr/bin/env python3
import os
import json
import re

base_dir = os.path.dirname(__file__)
stories_path = os.path.join(base_dir, 'stories.html')
json_path = os.path.join(base_dir, 'content_plan_30_hari.json')

with open(json_path, 'r', encoding='utf-8') as f:
    plan_data = json.load(f)

# Published Episodes (Hari 1 s.d 7)
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
        cover_html = f'''
          <div class="ep-cover">
            <img src="{cover_img}" alt="Adegan 1: {title}" width="400" height="250" loading="lazy">
          </div>'''
        link_html = f'<a href="{slug}" class="ep-link">Baca &amp; Lihat Komik →</a>'
    else:
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

# Add CSS for upcoming-cover
css_upcoming = """
    .ep-cover.upcoming-cover {
      background: linear-gradient(135deg, #0b1d40, #172033);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #fff;
      text-align: center;
      border-bottom: 3px solid var(--gold);
    }
    .upcoming-inner { padding: 25px 20px; }
    .upcoming-inner .ep-day-tag {
      display: inline-block;
      background: rgba(212, 160, 23, 0.2);
      color: #ffd86f;
      border: 1px solid var(--gold);
      padding: 4px 12px;
      border-radius: 99px;
      font-size: 11px;
      font-weight: 900;
      letter-spacing: .08em;
      margin-bottom: 8px;
    }
    .upcoming-inner b { display: block; font-size: 14px; color: #ffffff; font-weight: 800; }
    .upcoming-inner span { display: block; font-size: 12px; color: #94a3b8; margin-top: 4px; }
"""

if ".ep-cover.upcoming-cover" not in stories_html:
    stories_html = stories_html.replace("/* 30 Episode Catalog Table / Grid */", css_upcoming + "\n    /* 30 Episode Catalog Table / Grid */")

pattern = r'(<div class="episodes-grid">)(.*?)(</div>\s*</section>)'
replacement = r'\1\n' + new_grid_content + r'\n      \3'

updated_html = re.sub(pattern, replacement, stories_html, flags=re.DOTALL)

with open(stories_path, 'w', encoding='utf-8') as f:
    f.write(updated_html)

print("Updated stories.html: Hari 1-7 show unique real comic images. Hari 8-30 show sleek upcoming cards (ZERO duplication)!")
