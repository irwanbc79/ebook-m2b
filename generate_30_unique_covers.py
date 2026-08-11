#!/usr/bin/env python3
import os
import json
from PIL import Image, ImageDraw, ImageFont, ImageFilter

base_dir = os.path.dirname(__file__)
assets_dir = os.path.join(base_dir, 'assets')
json_path = os.path.join(base_dir, 'content_plan_30_hari.json')

with open(json_path, 'r', encoding='utf-8') as f:
    plan_data = json.load(f)

# Base background pools
b1 = Image.open(os.path.join(assets_dir, 'panel-1.png')).resize((800, 500))
b2 = Image.open(os.path.join(assets_dir, 'ep2-panel-1.png')).resize((800, 500))
b3 = Image.open(os.path.join(assets_dir, 'panel-2.png')).resize((800, 500))
b4 = Image.open(os.path.join(assets_dir, 'ep2-panel-3.png')).resize((800, 500))

base_pool = [b1, b2, b3, b4]

# Distinct color themes for 30 episodes
color_themes = [
    (11, 29, 64),    # 1: Navy
    (125, 8, 6),     # 2: Red
    (24, 121, 78),   # 3: Green
    (180, 83, 9),    # 4: Amber
    (3, 105, 161),   # 5: Sky Blue
    (109, 40, 217),  # 6: Purple
    (190, 18, 60),   # 7: Crimson
    (15, 118, 110),  # 8: Teal
    (194, 65, 12),   # 9: Orange
    (71, 85, 105),   # 10: Slate
    (161, 98, 7),    # 11: Bronze
    (13, 148, 136),  # 12: Emerald
    (159, 18, 57),   # 13: Rose
    (30, 58, 138),   # 14: Dark Blue
    (133, 77, 14),   # 15: Gold-Brown
    (67, 56, 202),   # 16: Indigo
    (5, 150, 105),   # 17: Mint Green
    (185, 28, 28),   # 18: Brick Red
    (3, 138, 240),   # 19: Electric Blue
    (147, 51, 234),  # 20: Violet
    (180, 83, 9),    # 21: Ochre
    (16, 185, 129),  # 22: Light Emerald
    (225, 29, 72),   # 23: Ruby
    (30, 41, 59),    # 24: Charcoal
    (217, 119, 6),   # 25: Amber Gold
    (88, 28, 135),   # 26: Deep Purple
    (14, 116, 144),  # 27: Cyan
    (202, 138, 4),   # 28: Olive Gold
    (153, 27, 27),   # 29: Maroon
    (11, 29, 64)     # 30: Master Navy Gold
]

def make_unique_cover(day, title, lead, category, case_text):
    theme_color = color_themes[(day - 1) % len(color_themes)]
    base_img = base_pool[(day - 1) % len(base_pool)].copy()
    
    # Slight tint / color filter to make visual look unique
    overlay_tint = Image.new("RGB", base_img.size, theme_color)
    blended = Image.blend(base_img, overlay_tint, alpha=0.28)
    
    draw = ImageDraw.Draw(blended)
    w, h = blended.size
    
    try:
        font_badge = ImageFont.truetype("/System/Library/Fonts/Helvetica.ttc", 22)
        font_title = ImageFont.truetype("/System/Library/Fonts/Helvetica.ttc", 24)
        font_sub = ImageFont.truetype("/System/Library/Fonts/Helvetica.ttc", 18)
    except:
        font_badge = ImageFont.load_default()
        font_title = ImageFont.load_default()
        font_sub = ImageFont.load_default()
        
    # Top Header Badge Bar
    draw.rectangle([(0, 0), (w, 54)], fill=theme_color)
    draw.rectangle([(0, 50), (w, 54)], fill=(212, 160, 23)) # Gold line
    draw.text((20, 14), f"HARI {day:02d} · {category.upper()} | LEAD: {lead.upper()}", fill=(255, 255, 255), font=font_badge)
    
    # Bottom Unique Speech Bubble Card
    draw.rectangle([(16, h - 165), (w - 16, h - 16)], fill=(255, 255, 255), outline=theme_color, width=3)
    draw.rectangle([(26, h - 180), (220, h - 155)], fill=theme_color)
    draw.text((34, h - 176), f"EPISODE {day:02d}", fill=(212, 160, 23), font=font_badge)
    
    # Wrap Title & Case Text
    words = title.split()
    lines = []
    curr = ""
    for word in words:
        if len(curr + " " + word) > 38:
            lines.append(curr)
            curr = word
        else:
            curr += " " + word if curr else word
    if curr:
        lines.append(curr)
        
    y_pos = h - 148
    for line in lines[:2]:
        draw.text((32, y_pos), line, fill=theme_color, font=font_title)
        y_pos += 28
        
    # Case short snippet
    snippet = case_text[:75] + "..." if len(case_text) > 75 else case_text
    draw.text((32, y_pos + 6), snippet, fill=(71, 85, 105), font=font_sub)
    
    return blended

# Generate all 30 unique cover images
for ep in plan_data:
    day = ep["day"]
    title = ep["title"]
    lead = ep["lead_character"]
    category = ep["category"]
    case_text = ep["case"]
    
    cover_img = make_unique_cover(day, title, lead, category, case_text)
    
    filename = "panel-1.png" if day == 1 else f"ep{day}-panel-1.png"
    save_path = os.path.join(assets_dir, filename)
    cover_img.save(save_path)
    print(f"Generated 100% Unique Cover for Day {day:02d}: {filename}")

print("SUCCESS: Generated 30 distinct cover images for all 30 days!")
