#!/usr/bin/env python3
"""Dokploy setup for ASSORELIE - creates project, service, domain, deploys"""

import subprocess, json, time, sys, os

EMAIL = "leguigou@gmail.com"
PASSWORD = os.environ.get("DOKPLOY_PASSWORD", "leDSKdu83!")
GIT_REPO = "https://github.com/leguigou/assorelie"
BRANCH = "main"

from playwright.sync_api import sync_playwright

def wait_and_click(page, selector, timeout=5000):
    try:
        el = page.locator(selector).first
        if el.is_visible(timeout=timeout):
            el.click(force=True)
            time.sleep(1)
            return True
    except:
        pass
    return False

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True, args=["--no-sandbox"])
    page = browser.new_page(viewport={"width": 1440, "height": 900})
    
    # === LOGIN ===
    print("🔐 Login...")
    page.goto("https://admin.deloffre.fr", wait_until="networkidle", timeout=30000)
    time.sleep(2)
    page.fill("input[name=email]", EMAIL)
    page.fill("input[name=password]", PASSWORD)
    page.click("button[type=submit]")
    time.sleep(4)
    
    if "login" in page.url.lower():
        print("❌ Login failed")
        page.screenshot(path="/tmp/dokploy-login-fail.png")
        browser.close()
        sys.exit(1)
    print("✅ Logged in")
    
    # === CREATE PROJECT ===
    print("📁 Creating project...")
    page.goto("https://admin.deloffre.fr/dashboard/projects", wait_until="networkidle", timeout=30000)
    time.sleep(3)
    page.screenshot(path="/tmp/dokploy-step0-projects.png")
    
    # Click "New Project" or "Create Project"
    if not wait_and_click(page, "button:has-text('New Project')") and \
       not wait_and_click(page, "button:has-text('Create Project')") and \
       not wait_and_click(page, "a:has-text('New Project')"):
        print("⚠️ Could not find create project button")
        page.screenshot(path="/tmp/dokploy-nobtn.png")
    
    time.sleep(3)
    
    # Fill form
    try:
        name_input = page.locator("input[name=name], input[id*=name], input[placeholder*=Name]").first
        if name_input.is_visible(timeout=3000):
            name_input.fill("assorelie")
            print("  Name filled")
    except:
        pass
    
    try:
        desc_input = page.locator("textarea[name=description]").first
        if desc_input.is_visible(timeout=2000):
            desc_input.fill("Site vitrine ASSORELIE - L'asso qui relie (Toulon)")
    except:
        pass
    
    time.sleep(1)
    page.screenshot(path="/tmp/dokploy-step1-create-form.png")
    
    # Submit
    if not wait_and_click(page, 'button[type="submit"]:has-text("Create")') and \
       not wait_and_click(page, "button:has-text('Create')"):
        # Try any submit button in dialog
        try:
            page.evaluate("document.querySelector('[role=\"dialog\"] button[type=\"submit\"]')?.click()")
            time.sleep(2)
        except:
            pass
    
    time.sleep(5)
    
    # Wait for redirect to project page
    print(f"📍 After create: {page.url[:100]}")
    page.screenshot(path="/tmp/dokploy-step2-after-create.png")
    
    # === ENVIRONNEMENT (production auto-créé) ===
    # Si on est sur la page du projet, trouver l'environnement
    env_link = page.locator("a:has-text('production')").first
    if env_link.is_visible(timeout=5000):
        env_link.click()
        time.sleep(3)
        print(f"📍 Env: {page.url[:100]}")
    
    page.screenshot(path="/tmp/dokploy-step3-env.png")
    
    # === CREATE SERVICE ===
    # Scroll up
    page.evaluate("window.scrollTo(0, 0)")
    time.sleep(1)
    
    if wait_and_click(page, "button:has-text('Create Service')"):
        time.sleep(2)
        print("  Create Service clicked")
        
        # Choisir Compose
        if wait_and_click(page, "button:has-text('Docker Compose')") or \
           wait_and_click(page, "button:has-text('Compose')"):
            time.sleep(3)
            print("  Compose selected")
    
    page.screenshot(path="/tmp/dokploy-step4-service-form.png")
    
    # === FILL SERVICE FORM ===
    # Name
    try:
        page.locator("input[name=name]").first.fill("assorelie-svc")
    except:
        pass
    
    # App name / container name
    try:
        page.locator("input[name=appName]").first.fill("assorelie")
    except:
        pass
    
    # Source: GitHub
    if wait_and_click(page, "button:has-text('GitHub')"):
        time.sleep(2)
        
        # Repository URL
        try:
            repo_input = page.locator("input[placeholder*=repository], input[name=repository]").first
            if repo_input.is_visible(timeout=3000):
                repo_input.fill(GIT_REPO)
                print(f"  Repo: {GIT_REPO}")
        except:
            pass
        
        # Branch
        try:
            branch_input = page.locator("input[placeholder*=branch], input[name=branch]").first
            if branch_input.is_visible(timeout=2000):
                branch_input.fill(BRANCH)
                print(f"  Branch: {BRANCH}")
        except:
            pass
    
    page.screenshot(path="/tmp/dokploy-step5-service-filled.png")
    
    # Submit creation
    time.sleep(1)
    if not wait_and_click(page, 'button[type="submit"]:has-text("Create")'):
        try:
            page.evaluate("document.querySelector('button[type=submit]')?.click()")
            time.sleep(3)
        except:
            pass
    
    time.sleep(5)
    print(f"📍 Service URL: {page.url[:100]}")
    page.screenshot(path="/tmp/dokploy-step6-service.png")
    
    # === DOCKER COMPOSE RAW ===
    # Switch to Raw mode
    if wait_and_click(page, "button:has-text('Raw')"):
        time.sleep(2)
        print("  Raw mode selected")
    
    compose_yml = """services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: assorelie
    restart: unless-stopped
    ports:
      - "80"
    networks:
      - dokploy-network
    volumes:
      - ./data:/var/www/html/data

networks:
  dokploy-network:
    external: true"""
    
    # Fill editor (CodeMirror .cm-content or textarea)
    try:
        cm = page.locator(".cm-content").first
        if cm.is_visible(timeout=3000):
            cm.click()
            time.sleep(0.5)
            page.keyboard.press("Control+A")
            time.sleep(0.3)
            page.keyboard.type(compose_yml, delay=5)
            print("  Composed typed into editor")
    except:
        try:
            textarea = page.locator("textarea").first
            if textarea.is_visible(timeout=2000):
                textarea.fill(compose_yml)
                print("  Composed typed into textarea")
        except:
            pass
    
    time.sleep(1)
    
    # Save
    if wait_and_click(page, "button:has-text('Save')"):
        time.sleep(3)
        print("✅ Compose saved")
    
    page.screenshot(path="/tmp/dokploy-step7-compse.png")
    
    # === DOMAIN ===
    if wait_and_click(page, "button:has-text('Domains')"):
        time.sleep(2)
        print("  Domains tab")
        
        if wait_and_click(page, "button:has-text('Add Domain')"):
            time.sleep(1)
            
            try:
                host_input = page.locator("input[name=host]").first
                if host_input.is_visible(timeout=2000):
                    host_input.fill("assorelie.deloffre.fr")
            except:
                pass
            
            try:
                port_input = page.locator("input[name=port]").first
                if port_input.is_visible(timeout=2000):
                    port_input.fill("80")
            except:
                pass
            
            # Click Add (in the modal table)
            wait_and_click(page, "button:has-text('Add')", timeout=2000)
            time.sleep(1)
            
            # Click Create (to save)
            if wait_and_click(page, "button:has-text('Create')"):
                time.sleep(3)
                print("✅ Domain added")
    
    page.screenshot(path="/tmp/dokploy-step8-domain.png")
    
    # === DEPLOY ===
    print("🚀 Deploying...")
    time.sleep(2)
    
    # Go back to service page if needed
    if "services/compose" not in page.url:
        # Try to navigate to the compose service
        page.goto(page.url.replace("/project/", "/dashboard/project/"))
        time.sleep(3)
    
    if wait_and_click(page, "button:has-text('Deploy')"):
        time.sleep(5)
        print("✅ Build started! Check admin.deloffre.fr for logs")
    else:
        print("⚠️ Deploy button not found - check screenshot")
    
    page.screenshot(path="/tmp/dokploy-step9-deploy.png")
    print("📸 All screenshots saved to /tmp/dokploy-*.png")
    
    browser.close()
    print("✅ Done!")
