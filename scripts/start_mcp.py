#!/usr/bin/env python3
"""Lance le MCP server et teste"""
import os, subprocess, time, json, urllib.request

os.chdir("/home/guillaume/assorelie")

API_KEY = "assore...024"

# Créer .env
with open(".env", "w") as f:
    f.write(f"MCP_API_KEY={API_KEY}\n")

# Tuer ancien processus
subprocess.run(["pkill", "-f", "mcp_server"], capture_output=True)
time.sleep(1)

# Lancer
env = {**os.environ, "MCP_API_KEY": API_KEY, "PORT": "8921"}
proc = subprocess.Popen(
    ["python3", "mcp_server.py", "--http"],
    stdout=open("/tmp/mcp_server.log", "w"),
    stderr=subprocess.STDOUT,
    env=env,
)
print(f"Serveur démarré PID: {proc.pid}")
time.sleep(4)

# Tester
try:
    req = urllib.request.Request(
        "http://localhost:8921/tools/list",
        headers={"Authorization": f"Bearer {API_KEY}"},
    )
    resp = urllib.request.urlopen(req, timeout=5)
    data = json.loads(resp.read().decode())
    tools = data.get("tools", data.get("result", {}).get("tools", []))
    print(f"✅ OK: {len(tools)} outils disponibles")
    for t in tools:
        print(f"  - {t['name']}")
except Exception as e:
    print(f"❌ Erreur: {e}")
    with open("/tmp/mcp_server.log") as f:
        print("LOGS:", f.read()[:500])
