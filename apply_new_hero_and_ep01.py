#!/usr/bin/env python3
import os
import shutil
from PIL import Image

base_dir = os.path.dirname(__file__)

# 1. New Hero Image from user's selection
src_hero = "/Users/irwanbece/.codex/generated_images/019fe9ad-936a-7de0-a9e5-bb0529f6cbb2/exec-c46bfe80-28a9-457e-8fe4-b15fdbbfbc25.png"
img_dir = os.path.join(base_dir, 'img')
assets_dir = os.path.join(base_dir, 'assets')
os.makedirs(img_dir, exist_ok=True)
os.makedirs(assets_dir, exist_ok=True)

if os.path.exists(src_hero):
    # Copy to png, jpg, webp in img/
    hero_img = Image.open(src_hero)
    hero_img.save(os.path.join(img_dir, 'ebook-cover-v2.png'))
    hero_img.save(os.path.join(assets_dir, 'm2b-hero-landing.png'))
    
    rgb_hero = hero_img.convert('RGB')
    rgb_hero.save(os.path.join(img_dir, 'ebook-cover-v2.jpg'), 'JPEG', quality=92)
    rgb_hero.save(os.path.join(img_dir, 'ebook-cover-v2.webp'), 'WEBP', quality=92)
    print("Updated Hero Image in img/ and assets/ from user selection!")

# 2. Episode 01 Composite Sheet
src_ep1 = "/Users/irwanbece/.codex/.chatgpt-projects/g-p-6934b2e10bac8191b7c737588ab32c7d/m2b-comic-blog-mockup/comics-final-episode-01-07/m2b-story-episode-01.png"
v2_dir = os.path.join(assets_dir, 'stories-v2')
os.makedirs(v2_dir, exist_ok=True)

if os.path.exists(src_ep1):
    dst_ep1 = os.path.join(v2_dir, 'm2b-story-episode-01.png')
    shutil.copy2(src_ep1, dst_ep1)
    print("Copied m2b-story-episode-01.png to assets/stories-v2/")

print("Finished asset preparations for Hero and Episode 01!")
