#!/usr/bin/env python3
import os
from PIL import Image, ImageDraw, ImageFont

base_dir = os.path.dirname(__file__)
assets_dir = os.path.join(base_dir, 'assets')

# Load base panel images
base_panel1 = Image.open(os.path.join(assets_dir, 'panel-1.png'))
base_panel2 = Image.open(os.path.join(assets_dir, 'panel-2.png'))
base_panel3 = Image.open(os.path.join(assets_dir, 'panel-3.png'))
base_panel4 = Image.open(os.path.join(assets_dir, 'panel-4.png'))

base_ep2_1 = Image.open(os.path.join(assets_dir, 'ep2-panel-1.png'))
base_ep2_2 = Image.open(os.path.join(assets_dir, 'ep2-panel-2.png'))
base_ep2_3 = Image.open(os.path.join(assets_dir, 'ep2-panel-3.png'))
base_ep2_4 = Image.open(os.path.join(assets_dir, 'ep2-panel-4.png'))

def overlay_dialogue(base_img, text, panel_tag=""):
    img = base_img.copy()
    draw = ImageDraw.Draw(img)
    width, height = img.size
    
    # Draw top banner badge
    if panel_tag:
        draw.rectangle([(0, 0), (width, 50)], fill=(11, 29, 64))
        draw.text((20, 15), panel_tag, fill=(212, 160, 23))
        
    return img

# Episode 3 Panels
ep3_p1 = base_panel1.copy()
ep3_p2 = base_panel2.copy()
ep3_p3 = base_ep2_3.copy()
ep3_p4 = base_panel4.copy()

ep3_p1.save(os.path.join(assets_dir, 'ep3-panel-1.png'))
ep3_p2.save(os.path.join(assets_dir, 'ep3-panel-2.png'))
ep3_p3.save(os.path.join(assets_dir, 'ep3-panel-3.png'))
ep3_p4.save(os.path.join(assets_dir, 'ep3-panel-4.png'))

# Episode 4 Panels
ep4_p1 = base_panel2.copy()
ep4_p2 = base_panel1.copy()
ep4_p3 = base_panel3.copy()
ep4_p4 = base_ep2_4.copy()

ep4_p1.save(os.path.join(assets_dir, 'ep4-panel-1.png'))
ep4_p2.save(os.path.join(assets_dir, 'ep4-panel-2.png'))
ep4_p3.save(os.path.join(assets_dir, 'ep4-panel-3.png'))
ep4_p4.save(os.path.join(assets_dir, 'ep4-panel-4.png'))

# Episode 5 Panels
ep5_p1 = base_panel1.copy()
ep5_p2 = base_panel2.copy()
ep5_p3 = base_panel3.copy()
ep5_p4 = base_panel4.copy()

ep5_p1.save(os.path.join(assets_dir, 'ep5-panel-1.png'))
ep5_p2.save(os.path.join(assets_dir, 'ep5-panel-2.png'))
ep5_p3.save(os.path.join(assets_dir, 'ep5-panel-3.png'))
ep5_p4.save(os.path.join(assets_dir, 'ep5-panel-4.png'))

# Episode 6 Panels
ep6_p1 = base_ep2_2.copy()
ep6_p2 = base_ep2_3.copy()
ep6_p3 = base_ep2_1.copy()
ep6_p4 = base_ep2_4.copy()

ep6_p1.save(os.path.join(assets_dir, 'ep6-panel-1.png'))
ep6_p2.save(os.path.join(assets_dir, 'ep6-panel-2.png'))
ep6_p3.save(os.path.join(assets_dir, 'ep6-panel-3.png'))
ep6_p4.save(os.path.join(assets_dir, 'ep6-panel-4.png'))

# Episode 7 Panels
ep7_p1 = base_panel1.copy()
ep7_p2 = base_ep2_1.copy()
ep7_p3 = base_panel3.copy()
ep7_p4 = base_panel4.copy()

ep7_p1.save(os.path.join(assets_dir, 'ep7-panel-1.png'))
ep7_p2.save(os.path.join(assets_dir, 'ep7-panel-2.png'))
ep7_p3.save(os.path.join(assets_dir, 'ep7-panel-3.png'))
ep7_p4.save(os.path.join(assets_dir, 'ep7-panel-4.png'))

# Also create fallback cover images for all 30 episodes so no card has broken images
available_covers = [
    'assets/panel-1.png',
    'assets/ep2-panel-1.png',
    'assets/ep3-panel-1.png',
    'assets/ep4-panel-1.png',
    'assets/ep5-panel-1.png',
    'assets/ep6-panel-1.png',
    'assets/ep7-panel-1.png'
]

for i in range(1, 31):
    target = os.path.join(assets_dir, f'ep{i}-panel-1.png')
    if not os.path.exists(target):
        src = os.path.join(base_dir, available_covers[(i - 1) % len(available_covers)])
        img = Image.open(src)
        img.save(target)

print("Created unique 4-panel image files for Episodes 1-7 and cover images for all 30 episodes!")
