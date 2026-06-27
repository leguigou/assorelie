#!/usr/bin/env python3
"""
ASSORELIE — MCP Server (FastMCP)
Permet à ChatGPT, Claude Desktop, etc. de gérer le site en français.

Usage:
  python3 mcp_server.py                # Mode stdio
  python3 mcp_server.py --http         # Mode HTTP (pour ChatGPT)
"""

import json, os, shutil, sys
from datetime import datetime
from pathlib import Path
from typing import Any

from mcp.server.fastmcp import FastMCP

# ─── Config ────────────────────────────────────────────────────────────

BASE_DIR = Path(__file__).parent.resolve()
DATA_DIR = BASE_DIR / "data"
DATA_DIR.mkdir(parents=True, exist_ok=True)

MCP_API_KEY = os.environ.get("MCP_API_KEY", "assorelie-mcp-dev")
PORT = int(os.environ.get("MCP_PORT", "8921"))


# ─── Utils ───────────────────────────────────────────────────────────────

def _load(filename: str) -> Any:
    path = DATA_DIR / filename
    if not path.exists():
        return [] if "s.json" in filename else {}
    return json.loads(path.read_text(encoding="utf-8"))


def _save(filename: str, data: Any) -> None:
    path = DATA_DIR / filename
    if path.exists():
        shutil.copy2(path, path.with_suffix(".json.bak"))
    path.write_text(json.dumps(data, ensure_ascii=False, indent=2), encoding="utf-8")


# ─── MCP Server ─────────────────────────────────────────────────────────

mcp = FastMCP("ASSORELIE", port=PORT)


@mcp.tool()
def liste_evenements() -> str:
    """Liste les événements à venir de l'association"""
    events = _load("events.json")
    now = datetime.now().strftime("%Y-%m-%d")
    upcoming = [e for e in events if isinstance(e, dict) and e.get("date", "") >= now]
    upcoming.sort(key=lambda e: e.get("date", ""))
    return json.dumps({"evenements": upcoming, "total": len(upcoming)}, ensure_ascii=False, indent=2)


@mcp.tool()
def ajouter_evenement(titre: str, date: str, description: str,
                       time: str = "18h00", location: str = "Toulon",
                       link: str = "") -> str:
    """Ajoute un événement à l'agenda"""
    events = _load("events.json")
    new = {
        "id": max((e.get("id", 0) for e in events if isinstance(e, dict)), default=0) + 1,
        "title": titre, "date": date, "time": time,
        "location": location, "description": description,
        "link": link or None,
    }
    events.append(new)
    _save("events.json", events)
    return json.dumps({"success": True, "evenement": new}, ensure_ascii=False, indent=2)


@mcp.tool()
def modifier_evenement(id_event: int, titre: str = None, date: str = None,
                        time: str = None, location: str = None,
                        description: str = None) -> str:
    """Modifie un événement existant"""
    events = _load("events.json")
    mapping = {"titre": "title", "date": "date", "time": "time",
               "location": "location", "description": "description"}
    for e in events:
        if isinstance(e, dict) and e.get("id") == id_event:
            for k, v in mapping.items():
                val = locals().get(k)
                if val is not None:
                    e[v] = val
            _save("events.json", events)
            return json.dumps({"success": True, "evenement": e}, ensure_ascii=False, indent=2)
    return json.dumps({"success": False, "error": f"Événement #{id_event} non trouvé"})


@mcp.tool()
def supprimer_evenement(id_event: int) -> str:
    """Supprime un événement"""
    events = _load("events.json")
    new = [e for e in events if not (isinstance(e, dict) and e.get("id") == id_event)]
    if len(new) == len(events):
        return json.dumps({"success": False, "error": f"Événement #{id_event} non trouvé"})
    _save("events.json", new)
    return json.dumps({"success": True, "message": f"Événement #{id_event} supprimé"})


@mcp.tool()
def lire_infos_asso() -> str:
    """Affiche les informations de l'association"""
    config = _load("config.json")
    return json.dumps(config, ensure_ascii=False, indent=2)


@mcp.tool()
def modifier_infos_asso(champ: str, valeur: str) -> str:
    """Modifie les infos de l'association"""
    config = _load("config.json")
    if champ in config.get("association", {}):
        config["association"][champ] = valeur
        _save("config.json", config)
        return json.dumps({"success": True, "message": f"{champ} mis à jour"})
    return json.dumps({"success": False, "error": f"Champ '{champ}' invalide"})


@mcp.tool()
def liste_membres() -> str:
    """Liste les membres de l'association"""
    membres = _load("members.json")
    return json.dumps({"membres": membres, "total": len(membres)}, ensure_ascii=False, indent=2)


@mcp.tool()
def ajouter_membre(nom: str, email: str, telephone: str = "",
                    statut: str = "membre") -> str:
    """Ajoute un membre"""
    membres = _load("members.json")
    new = {
        "id": max((m.get("id", 0) for m in membres if isinstance(m, dict)), default=0) + 1,
        "name": nom, "email": email, "phone": telephone,
        "status": statut, "created_at": datetime.now().strftime("%Y-%m-%d"),
    }
    membres.append(new)
    _save("members.json", membres)
    return json.dumps({"success": True, "membre": new}, ensure_ascii=False, indent=2)


@mcp.tool()
def supprimer_membre(id_membre: int) -> str:
    """Supprime un membre"""
    membres = _load("members.json")
    new = [m for m in membres if not (isinstance(m, dict) and m.get("id") == id_membre)]
    if len(new) == len(membres):
        return json.dumps({"success": False, "error": f"Membre #{id_membre} non trouvé"})
    _save("members.json", new)
    return json.dumps({"success": True, "message": f"Membre #{id_membre} supprimé"})


# ─── Main ────────────────────────────────────────────────────────────────

if __name__ == "__main__":
    if "--http" in sys.argv:
        print(f"🌐 MCP Server: http://0.0.0.0:{PORT}", file=sys.stderr)
        print(f"🔑 Auth: {MCP_API_KEY}", file=sys.stderr)
        mcp.run(transport="sse")
    else:
        mcp.run(transport="stdio")
