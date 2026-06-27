#!/usr/bin/env python3
"""Test complet du MCP server ASSORELIE"""
import json, ssl, threading, time, urllib.request

MCP_URL = "https://mcp.assorelie.deloffre.fr"
MCP_KEY = "assorelie-mcp-dev"

ssl_ctx = ssl.create_default_context()
ssl_ctx.check_hostname = False
ssl_ctx.verify_mode = ssl.CERT_NONE

def call_tool(tool_name, args=None):
    """Appelle un outil MCP via la session SSE"""
    import http.client
    
    # 1. Connexion SSE
    conn = http.client.HTTPSConnection("mcp.assorelie.deloffre.fr", context=ssl_ctx)
    conn.request("GET", "/sse", headers={"Authorization": f"Bearer {MCP_KEY}"})
    resp = conn.getresponse()
    
    if resp.status != 200:
        print(f"❌ SSE: HTTP {resp.status}")
        return
    
    print(f"✅ SSE connecté (HTTP {resp.status})")
    
    # Lire le premier event SSE pour obtenir le endpoint
    data = b""
    while True:
        chunk = resp.read(1)
        if not chunk:
            break
        data += chunk
        if b"\n\n" in data:
            break
    
    print(f"   SSE data: {data.decode()[:200]}")
    
    # Extraire l'URL de session
    endpoint_url = None
    for line in data.decode().split("\n"):
        if line.startswith("event: endpoint"):
            continue
        if line.startswith("data: "):
            endpoint_url = line[6:].strip()
    
    if not endpoint_url:
        print("❌ Pas d'endpoint trouvé")
        return
    
    print(f"   Endpoint: {endpoint_url}")
    resp.close()
    
    # 2. POST un appel d'outil
    payload = json.dumps({"jsonrpc": "2.0", "method": "tools/call", "params": {"name": tool_name, "arguments": args or {}}, "id": 1})
    
    conn2 = http.client.HTTPSConnection("mcp.assorelie.deloffre.fr", context=ssl_ctx)
    conn2.request("POST", endpoint_url, body=payload, headers={
        "Authorization": f"Bearer {MCP_KEY}",
        "Content-Type": "application/json",
    })
    resp2 = conn2.getresponse()
    result = json.loads(resp2.read().decode())
    print(f"✅ Réponse: {json.dumps(result, indent=2, ensure_ascii=False)[:500]}")
    resp2.close()

if __name__ == "__main__":
    print("🔍 Test MCP ASSORELIE\n")
    call_tool("liste_evenements")
