#!/usr/bin/env python3
"""
ASSORELIE — MCP Server (FastMCP)
Permet à ChatGPT, Claude Desktop, etc. de gérer le site en français.

Usage:
  python3 mcp_server.py                # Mode stdio
  python3 mcp_server.py --http         # Mode HTTP (pour ChatGPT)
"""

import json, os, secrets, sys
from datetime import datetime

from pydantic import AnyHttpUrl
from mcp.server.auth.provider import AccessToken, TokenVerifier
from mcp.server.auth.settings import AuthSettings
from mcp.server.fastmcp import FastMCP

from assorelie_database import audit, database, fetch_site_config

# ─── Config ────────────────────────────────────────────────────────────

PORT = int(os.environ.get("MCP_PORT", "8921"))
HTTP_MODE = "--http" in sys.argv
MCP_API_KEY = os.environ.get("MCP_API_KEY")
MCP_BASE_URL = os.environ.get("MCP_BASE_URL", f"http://localhost:{PORT}").rstrip("/")
MCP_SCOPE = "assorelie:manage"


class StaticTokenVerifier(TokenVerifier):
    """Valide la clé Bearer configurée dans l'environnement."""

    def __init__(self, api_key: str):
        self._api_key = api_key

    async def verify_token(self, token: str) -> AccessToken | None:
        if not secrets.compare_digest(token, self._api_key):
            return None
        return AccessToken(
            token=token,
            client_id="assorelie-mcp-client",
            scopes=[MCP_SCOPE],
        )

# ─── MCP Server ─────────────────────────────────────────────────────────

if HTTP_MODE:
    if not MCP_API_KEY:
        raise SystemExit(
            "MCP_API_KEY est obligatoire en mode HTTP. "
            "Configurez une clé secrète dans l'environnement."
        )
    if len(MCP_API_KEY) < 32:
        raise SystemExit(
            "MCP_API_KEY doit contenir au moins 32 caractères. "
            "Générez-la avec secrets.token_urlsafe(48)."
        )

    mcp = FastMCP(
        "ASSORELIE",
        port=PORT,
        host="0.0.0.0",
        token_verifier=StaticTokenVerifier(MCP_API_KEY),
        auth=AuthSettings(
            issuer_url=AnyHttpUrl(MCP_BASE_URL),
            resource_server_url=AnyHttpUrl(MCP_BASE_URL),
            required_scopes=[MCP_SCOPE],
        ),
    )
else:
    # Le transport stdio est local et ne passe pas par HTTP.
    mcp = FastMCP("ASSORELIE")


@mcp.tool()
def liste_evenements() -> str:
    """Liste les événements à venir de l'association"""
    now = datetime.now().strftime("%Y-%m-%d")

    with database() as connection:
        rows = connection.execute(
            """
            SELECT id, title, date, time, location, description, link
            FROM events
            ORDER BY
                CASE WHEN date < ? THEN 1 ELSE 0 END ASC,
                CASE WHEN date >= ? THEN date END ASC,
                CASE WHEN date < ? THEN date END DESC
            """,
            (now, now, now),
        ).fetchall()

    events = []
    for row in rows:
        event = dict(row)
        event["past"] = event["date"] < now
        events.append(event)

    return json.dumps({"evenements": events, "total": len(events)}, ensure_ascii=False, indent=2)


@mcp.tool()
def ajouter_evenement(titre: str, date: str, description: str,
                       time: str = "18h00", location: str = "Toulon",
                       link: str = "") -> str:
    """Ajoute un événement à l'agenda"""
    with database() as connection:
        cursor = connection.execute(
            """
            INSERT INTO events (title, date, time, location, description, link)
            VALUES (?, ?, ?, ?, ?, ?)
            """,
            (titre, date, time, location, description, link or None),
        )
        event_id = cursor.lastrowid
        row = connection.execute(
            """
            SELECT id, title, date, time, location, description, link
            FROM events WHERE id = ?
            """,
            (event_id,),
        ).fetchone()
        audit(connection, "create", "event", event_id)

    new = dict(row)
    return json.dumps({"success": True, "evenement": new}, ensure_ascii=False, indent=2)


@mcp.tool()
def modifier_evenement(id_event: int, titre: str = None, date: str = None,
                        time: str = None, location: str = None,
                        description: str = None, link: str = None) -> str:
    """Modifie un événement existant"""
    values = {
        "title": titre,
        "date": date,
        "time": time,
        "location": location,
        "description": description,
        "link": link,
    }
    updates = {column: value for column, value in values.items() if value is not None}

    with database() as connection:
        exists = connection.execute(
            "SELECT 1 FROM events WHERE id = ?", (id_event,)
        ).fetchone()
        if exists is None:
            return json.dumps(
                {"success": False, "error": f"Événement #{id_event} non trouvé"},
                ensure_ascii=False,
            )

        if updates:
            assignments = ", ".join(f"{column} = ?" for column in updates)
            connection.execute(
                f"UPDATE events SET {assignments}, updated_at = CURRENT_TIMESTAMP "
                "WHERE id = ?",
                (*updates.values(), id_event),
            )
            audit(
                connection,
                "update",
                "event",
                id_event,
                {"fields": list(updates)},
            )

        row = connection.execute(
            """
            SELECT id, title, date, time, location, description, link
            FROM events WHERE id = ?
            """,
            (id_event,),
        ).fetchone()

    return json.dumps(
        {"success": True, "evenement": dict(row)},
        ensure_ascii=False,
        indent=2,
    )


@mcp.tool()
def supprimer_evenement(id_event: int) -> str:
    """Supprime un événement"""
    with database() as connection:
        cursor = connection.execute("DELETE FROM events WHERE id = ?", (id_event,))
        if cursor.rowcount == 0:
            return json.dumps(
                {"success": False, "error": f"Événement #{id_event} non trouvé"},
                ensure_ascii=False,
            )
        audit(connection, "delete", "event", id_event)

    return json.dumps({"success": True, "message": f"Événement #{id_event} supprimé"})


@mcp.tool()
def lire_infos_asso() -> str:
    """Affiche les informations de l'association"""
    with database() as connection:
        config = fetch_site_config(connection)
    return json.dumps(config, ensure_ascii=False, indent=2)


@mcp.tool()
def modifier_infos_asso(champ: str, valeur: str) -> str:
    """Modifie les infos de l'association"""
    with database() as connection:
        exists = connection.execute(
            """
            SELECT 1 FROM settings
            WHERE section = 'association' AND key = ?
            """,
            (champ,),
        ).fetchone()
        if exists is None:
            return json.dumps(
                {"success": False, "error": f"Champ '{champ}' invalide"},
                ensure_ascii=False,
            )

        connection.execute(
            """
            UPDATE settings
            SET value = ?, updated_at = CURRENT_TIMESTAMP
            WHERE section = 'association' AND key = ?
            """,
            (valeur, champ),
        )
        audit(connection, "update", "setting", f"association.{champ}")

    return json.dumps(
        {"success": True, "message": f"{champ} mis à jour"},
        ensure_ascii=False,
    )


@mcp.tool()
def liste_membres() -> str:
    """Liste les membres de l'association"""
    with database() as connection:
        membres = [
            dict(row)
            for row in connection.execute(
                """
                SELECT id, name, email, phone, status, created_at, updated_at
                FROM members
                ORDER BY name COLLATE NOCASE ASC, id ASC
                """
            )
        ]
    return json.dumps({"membres": membres, "total": len(membres)}, ensure_ascii=False, indent=2)


@mcp.tool()
def ajouter_membre(nom: str, email: str, telephone: str = "",
                    statut: str = "membre") -> str:
    """Ajoute un membre"""
    created_at = datetime.now().strftime("%Y-%m-%d")
    with database() as connection:
        cursor = connection.execute(
            """
            INSERT INTO members (name, email, phone, status, created_at)
            VALUES (?, ?, ?, ?, ?)
            """,
            (nom, email, telephone, statut, created_at),
        )
        member_id = cursor.lastrowid
        row = connection.execute(
            """
            SELECT id, name, email, phone, status, created_at, updated_at
            FROM members WHERE id = ?
            """,
            (member_id,),
        ).fetchone()
        audit(connection, "create", "member", member_id)

    new = dict(row)
    return json.dumps({"success": True, "membre": new}, ensure_ascii=False, indent=2)


@mcp.tool()
def supprimer_membre(id_membre: int) -> str:
    """Supprime un membre"""
    with database() as connection:
        cursor = connection.execute("DELETE FROM members WHERE id = ?", (id_membre,))
        if cursor.rowcount == 0:
            return json.dumps(
                {"success": False, "error": f"Membre #{id_membre} non trouvé"},
                ensure_ascii=False,
            )
        audit(connection, "delete", "member", id_membre)

    return json.dumps({"success": True, "message": f"Membre #{id_membre} supprimé"})


# ─── Main ────────────────────────────────────────────────────────────────

if __name__ == "__main__":
    if HTTP_MODE:
        print(f"🌐 MCP Server: http://0.0.0.0:{PORT}", file=sys.stderr)
        print("🔐 Authentification Bearer activée", file=sys.stderr)
        mcp.run(transport="sse")
    else:
        mcp.run(transport="stdio")
