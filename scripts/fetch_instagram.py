#!/usr/bin/env python3
"""
Script de synchronisation Instagram pour ASSORELIE
Récupère les dernières photos publiques du compte @assorelie
et les stocke dans data/gallery.json

Usage: python3 scripts/fetch_instagram.py
"""

import json
import os
import re
import sys
import urllib.request

USERNAME = "assorelie"
BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OUTPUT_FILE = os.path.join(BASE_DIR, "data", "gallery.json")
MAX_PHOTOS = 12
USER_AGENT = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36"


def fetch_instagram_photos(username: str) -> list[dict]:
    """Récupère les dernières photos Instagram depuis le HTML public."""
    
    url = f"https://www.instagram.com/{username}/"
    
    req = urllib.request.Request(url, headers={
        "User-Agent": USER_AGENT,
        "Accept": "text/html",
    })
    
    try:
        with urllib.request.urlopen(req, timeout=20) as resp:
            html = resp.read().decode("utf-8", errors="replace")
    except Exception as e:
        print(f"❌ Erreur HTTP: {e}", file=sys.stderr)
        return []
    
    photos = []
    seen_urls = set()
    
    # Méthode 1: window.__INITIAL_STATE__ (le plus complet)
    match = re.search(r'window\.__INITIAL_STATE__\s*=\s*({.*?});', html, re.DOTALL)
    
    if match:
        try:
            data = json.loads(match.group(1))
            
            def walk_for_media(obj, depth=0):
                nonlocal photos
                if depth > 15 or len(photos) >= MAX_PHOTOS:
                    return True
                if isinstance(obj, dict):
                    # Chercher les edges de timeline
                    if "edge_owner_to_timeline_media" in obj:
                        edges = obj["edge_owner_to_timeline_media"].get("edges", [])
                        for edge in edges:
                            node = edge.get("node", {})
                            display_url = node.get("display_url") or node.get("thumbnail_src", "")
                            if display_url and display_url not in seen_urls:
                                seen_urls.add(display_url)
                                caption = ""
                                caption_data = node.get("edge_media_to_caption", {})
                                if caption_data.get("edges"):
                                    caption = caption_data["edges"][0].get("node", {}).get("text", "")
                                photos.append({
                                    "url": display_url,
                                    "thumbnail": node.get("thumbnail_src", display_url),
                                    "caption": caption[:200],
                                    "timestamp": node.get("taken_at_timestamp", 0),
                                    "id": node.get("id", ""),
                                    "shortcode": node.get("shortcode", ""),
                                    "type": "video" if node.get("is_video") else "image",
                                    "likes": node.get("edge_liked_by", {}).get("count", 0),
                                })
                                if len(photos) >= MAX_PHOTOS:
                                    return True
                        return True if photos else None
                    for val in obj.values():
                        if isinstance(val, (dict, list)):
                            if walk_for_media(val, depth + 1):
                                return True
                elif isinstance(obj, list):
                    for item in obj:
                        if isinstance(item, (dict, list)):
                            if walk_for_media(item, depth + 1):
                                return True
                return None
            
            walk_for_media(data)
        except Exception as e:
            print(f"⚠️ Erreur parsing: {e}", file=sys.stderr)
    
    if photos:
        print(f"✅ {len(photos)} photos récupérées")
        return photos
    
    # Méthode 2: Fallback — meta og:image
    og_images = re.findall(r'<meta\s+property="og:image"[^>]*content="([^"]+)"', html)
    unique_og = list(dict.fromkeys(og_images))
    if unique_og:
        photos = [{"url": u, "thumbnail": u, "caption": "", "type": "image"} for u in unique_og[:MAX_PHOTOS]]
        print(f"✅ Fallback: {len(photos)} photos via og:image")
        return photos
    
    print("❌ Aucune photo trouvée", file=sys.stderr)
    return []


def save_gallery(photos: list[dict]):
    """Sauvegarde la galerie en JSON."""
    os.makedirs(os.path.dirname(OUTPUT_FILE), exist_ok=True)
    
    from datetime import datetime
    data = {
        "source": f"https://www.instagram.com/{USERNAME}/",
        "username": USERNAME,
        "updated_at": datetime.now().isoformat(),
        "total": len(photos),
        "photos": photos
    }
    
    with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, indent=2)
    
    print(f"💾 Sauvegardé: {OUTPUT_FILE} ({len(photos)} photos)")
    return OUTPUT_FILE


if __name__ == "__main__":
    print(f"📸 Instagram @{USERNAME}...")
    photos = fetch_instagram_photos(USERNAME)
    
    if photos:
        save_gallery(photos)
        for p in photos[:5]:
            cap = (p.get("caption") or "")[:60]
            print(f"  • {cap or '(légende vide)'}")
        if len(photos) > 5:
            print(f"  ... et {len(photos) - 5} autres")
    else:
        print("❌ Échec")
        sys.exit(1)
