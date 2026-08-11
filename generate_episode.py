#!/usr/bin/env python3
import sys
import os
import json
import urllib.request

# Load OPENAI_API_KEY from .env
env_path = os.path.join(os.path.dirname(__file__), '.env')
api_key = None
if os.path.exists(env_path):
    with open(env_path) as f:
        for line in f:
            if line.startswith('OPENAI_API_KEY='):
                api_key = line.strip().split('=', 1)[1]

if not api_key:
    print("Error: OPENAI_API_KEY not found in .env")
    sys.exit(1)

def generate_dalle_image(prompt, output_file):
    print(f"Generating DALL-E 3 image for: {output_file}...")
    url = "https://api.openai.com/v1/images/generations"
    payload = {
        "model": "dall-e-3",
        "prompt": prompt,
        "n": 1,
        "size": "1024x1024",
        "quality": "standard",
        "response_format": "url"
    }
    headers = {
        "Authorization": f"Bearer {api_key}",
        "Content-Type": "application/json"
    }
    req = urllib.request.Request(url, data=json.dumps(payload).encode('utf-8'), headers=headers)
    try:
        with urllib.request.urlopen(req) as resp:
            res_data = json.loads(resp.read().decode('utf-8'))
            img_url = res_data['data'][0]['url']
            print(f"Image URL received. Downloading to {output_file}...")
            urllib.request.urlretrieve(img_url, output_file)
            print(f"Successfully saved image: {output_file}")
            return True
    except Exception as e:
        print(f"DALL-E 3 Generation Error: {e}")
        return False

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python3 generate_episode.py <episode_number>")
        sys.exit(1)
    
    ep_num = int(sys.argv[1])
    print(f"Starting auto-generation process for Episode {ep_num:02d}...")
    
    # Load JSON plan
    plan_path = os.path.join(os.path.dirname(__file__), 'content_plan_30_hari.json')
    with open(plan_path) as f:
        plan = json.load(f)
    
    ep_data = next((item for item in plan if item["day"] == ep_num), None)
    if not ep_data:
        print(f"Error: Episode {ep_num} not found in content plan!")
        sys.exit(1)
        
    print(f"Title: {ep_data['title']}")
    print(f"Lead: {ep_data['lead_character']}")
    print(f"Case: {ep_data['case']}")

    img_prompt = (
        f"Corporate 4-panel comic strip on clean white background with bold navy borders separating the 4 panels. "
        f"Featuring Indonesian logistics characters: {ep_data['lead_character']} (M2B specialist in navy polo shirt) "
        f"and Yusuf (businessman in formal shirt). "
        f"Theme: {ep_data['title']}. Case: {ep_data['case']}. "
        f"Panel 1: Problem discovery. Panel 2: Detailed document/cargo inspection. "
        f"Panel 3: Discussion of customs regulations. Panel 4: Resolution & agreement. "
        f"Clean vector comic style, vibrant colors, clear professional atmosphere."
    )
    
    output_img = os.path.join(os.path.dirname(__file__), 'assets', f'comic-episode-{ep_num:02d}.png')
    generate_dalle_image(img_prompt, output_img)
