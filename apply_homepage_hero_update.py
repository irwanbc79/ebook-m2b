#!/usr/bin/env python3
import os
import shutil
import re

base_dir = os.path.dirname(__file__)

# 1. Copy m2b-homepage-general-8-tiles.png to assets/stories-v2/
src_img = "/Users/irwanbece/.codex/.chatgpt-projects/g-p-6934b2e10bac8191b7c737588ab32c7d/m2b-comic-blog-mockup/homepage-hero/m2b-homepage-general-8-tiles.png"
v2_dir = os.path.join(base_dir, 'assets', 'stories-v2')
os.makedirs(v2_dir, exist_ok=True)

dst_img = os.path.join(v2_dir, 'm2b-homepage-general-8-tiles.png')

if os.path.exists(src_img):
    shutil.copy2(src_img, dst_img)
    print("Copied m2b-homepage-general-8-tiles.png to assets/stories-v2/")
else:
    print(f"ERROR: Source image not found at {src_img}")

# 2. Update stories.html hero section & CSS
stories_path = os.path.join(base_dir, 'stories.html')

with open(stories_path, 'r', encoding='utf-8') as f:
    stories_html = f.read()

# Add CSS for .hero-comic--overview if not present
hero_css = """
    .hero-comic--overview {
      width: 100%;
      margin: 0;
    }
    .hero-comic--overview img {
      display: block;
      width: 100%;
      height: auto;
      border: 2px solid #0b1d40;
      border-radius: 18px;
      background: #fff;
      box-shadow: 18px 18px 0 #d4a017;
    }
    @media (max-width: 720px) {
      .hero-comic--overview img {
        border-radius: 12px;
        box-shadow: 8px 8px 0 #d4a017;
      }
    }
"""

if ".hero-comic--overview" not in stories_html:
    stories_html = stories_html.replace("/* Responsive Rules */", hero_css + "\n    /* Responsive Rules */")

# Update Hero Section HTML
old_hero_pattern = r'(<section class="hero">.*?<div class="hero-grid shell">)(.*?)(</section>)'

new_hero_body = """
        <div class="hero-copy">
          <span class="eyebrow">M2B Logistics Stories</span>
          <h1>Satu shipment.<br><span>Banyak titik risiko.</span></h1>
          <p>Ikuti bagaimana tim M2B mengoordinasikan dokumen, kepatuhan, vendor, biaya, hingga barang diterima—melalui cerita visual yang ringkas dan mudah dipahami.</p>
          <div class="hero-actions">
            <a class="button" href="#cerita">Jelajahi 30 Episode</a>
            <a class="button secondary" href="episode-01.html">Mulai dari Episode 01</a>
            <a class="button secondary" href="index.html#order" style="border-color:var(--gold); color:var(--navy);">Order E-book Rp 49rb</a>
          </div>
          <div class="trust">
            <span>Bahasa sederhana</span>
            <span>Kasus operasional</span>
            <span>Diperiksa praktisi</span>
          </div>
        </div>
        <figure class="hero-comic hero-comic--overview">
          <img
            src="assets/stories-v2/m2b-homepage-general-8-tiles.png"
            alt="Delapan tahapan koordinasi M2B dari kebutuhan pengiriman, pemeriksaan dokumen, kepabeanan, operasional lapangan, hingga POD"
            width="1672"
            height="941"
            loading="eager"
            fetchpriority="high"
            decoding="async"
          >
        </figure>
      </div>
"""

updated_stories = re.sub(old_hero_pattern, r'\1' + new_hero_body + r'\n    \3', stories_html, flags=re.DOTALL)

with open(stories_path, 'w', encoding='utf-8') as f:
    f.write(updated_stories)

print("Successfully updated stories.html hero copy and 8-tile general journey hero image!")
