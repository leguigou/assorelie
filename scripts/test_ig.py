#!/usr/bin/env python3
"""Test Instagram scraping with Playwright"""
import json, re, sys, time
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True, args=["--no-sandbox"])
    page = browser.new_page(viewport={"width": 390, "height": 844})
    
    print("Loading Instagram...", file=sys.stderr)
    page.goto("https://www.instagram.com/assorelie/", wait_until="domcontentloaded", timeout=30000)
    time.sleep(8)
    
    html = page.content()
    print(f"HTML size: {len(html)}", file=sys.stderr)
    
    # Chercher les JSON dans les scripts
    scripts = page.locator("script[type='text/javascript']").all()
    for script in scripts[:5]:
        text = script.text_content() or ""
        if "display_url" in text:
            urls = re.findall(r'"display_url"\s*:\s*"([^"]+)"', text)
            print(f"Found {len(urls)} display_url in script", file=sys.stderr)
            for u in urls[:3]:
                print(f"  {u[:100]}", file=sys.stderr)
    
    # Chercher les données dans window.__INITIAL_STATE__
    data = page.evaluate("() => { try { return JSON.stringify(window.__INITIAL_STATE__); } catch(e) { return null; } }")
    if data:
        print(f"__INITIAL_STATE__ found, size: {len(data)}", file=sys.stderr)
        
        parsed = json.loads(data)
        # Chercher les médias
        def find_media(obj, depth=0):
            if depth > 20:
                return None
            if isinstance(obj, dict):
                if "edge_owner_to_timeline_media" in obj:
                    return obj["edge_owner_to_timeline_media"].get("edges", [])
                for v in obj.values():
                    r = find_media(v, depth + 1)
                    if r is not None:
                        return r
            elif isinstance(obj, list):
                for item in obj:
                    r = find_media(item, depth + 1)
                    if r is not None:
                        return r
            return None
        
        edges = find_media(parsed)
        if edges:
            photos = []
            for edge in edges[:12]:
                node = edge.get("node", {})
                url = node.get("display_url", "")
                thumb = node.get("thumbnail_src", url)
                caption = ""
                cap_edges = node.get("edge_media_to_caption", {}).get("edges", [])
                if cap_edges:
                    caption = cap_edges[0].get("node", {}).get("text", "")[:200]
                photos.append({
                    "url": url,
                    "thumbnail": thumb,
                    "caption": caption,
                    "timestamp": node.get("taken_at_timestamp", 0),
                    "shortcode": node.get("shortcode", ""),
                    "type": "video" if node.get("is_video") else "image",
                    "likes": node.get("edge_liked_by", {}).get("count", 0),
                })
            
            result = {
                "source": "https://www.instagram.com/assorelie/",
                "username": "assorelie",
                "updated_at": __import__("datetime").datetime.now().isoformat(),
                "total": len(photos),
                "photos": photos,
            }
            print(json.dumps(result, ensure_ascii=False))
        else:
            print("NO_MEDIA", file=sys.stderr)
            # Dump la structure pour debug
            print(json.dumps(list(parsed.keys())[:20], indent=2), file=sys.stderr)
    else:
        print("NO_INITIAL_STATE", file=sys.stderr)
        # Chercher les meta
        og = re.findall(r'og:image[^>]*content="([^"]+)"', html)
        print(f"og:images: {len(og)}", file=sys.stderr)
        if og:
            result = {"source": "https://www.instagram.com/assorelie/", "username": "assorelie", "photos": [{"url": u, "thumbnail": u} for u in og[:12]]}
            print(json.dumps(result, ensure_ascii=False))
    
    page.screenshot(path="/tmp/ig-assorelie.png")
    print("Screenshot saved", file=sys.stderr)
    browser.close()
