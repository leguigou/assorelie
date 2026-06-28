"""Accès SQLite partagé par le serveur MCP ASSORELIE."""

from __future__ import annotations

import json
import os
import sqlite3
from contextlib import contextmanager
from datetime import datetime
from pathlib import Path
from typing import Any, Iterator

BASE_DIR = Path(__file__).parent.resolve()
DATA_DIR = BASE_DIR / "data"
SEED_DIR = Path(os.environ.get("ASSORELIE_SEED_DIR", DATA_DIR))
DB_PATH = Path(
    os.environ.get("ASSORELIE_DB_PATH", DATA_DIR / "assorelie.sqlite")
)
SCHEMA_PATH = BASE_DIR / "database" / "schema.sql"


def _connect() -> sqlite3.Connection:
    DB_PATH.parent.mkdir(parents=True, exist_ok=True)

    connection = sqlite3.connect(DB_PATH, timeout=5)
    connection.row_factory = sqlite3.Row
    connection.execute("PRAGMA foreign_keys = ON")
    connection.execute("PRAGMA busy_timeout = 5000")
    connection.execute("PRAGMA journal_mode = WAL")
    connection.execute("PRAGMA synchronous = NORMAL")
    _initialize(connection)
    return connection


@contextmanager
def database() -> Iterator[sqlite3.Connection]:
    connection = _connect()
    try:
        yield connection
        connection.commit()
    except Exception:
        connection.rollback()
        raise
    finally:
        connection.close()


def _initialize(connection: sqlite3.Connection) -> None:
    version = connection.execute("PRAGMA user_version").fetchone()[0]
    if version >= 1:
        return

    schema = SCHEMA_PATH.read_text(encoding="utf-8")
    connection.execute("BEGIN IMMEDIATE")

    try:
        for statement in schema.split(";"):
            if statement.strip():
                connection.execute(statement)

        _import_json_data(connection)
        connection.execute("PRAGMA user_version = 1")
        connection.commit()

        try:
            DB_PATH.chmod(0o664)
        except OSError:
            pass
    except Exception:
        connection.rollback()
        raise


def _import_json_data(connection: sqlite3.Connection) -> None:
    if not _meta_exists(connection, "config_json_imported"):
        config = _read_json(SEED_DIR / "config.json", {})

        for section in ("association", "social"):
            for key, value in config.get(section, {}).items():
                connection.execute(
                    """
                    INSERT OR REPLACE INTO settings (section, key, value)
                    VALUES (?, ?, ?)
                    """,
                    (section, str(key), str(value)),
                )

        for position, activity in enumerate(config.get("activities", [])):
            connection.execute(
                """
                INSERT INTO activities (icon, title, description, sort_order)
                VALUES (?, ?, ?, ?)
                """,
                (
                    activity.get("icon", ""),
                    activity.get("title", ""),
                    activity.get("description", ""),
                    position,
                ),
            )

        _set_meta(connection, "config_json_imported")

    if not _meta_exists(connection, "events_json_imported"):
        for event in _read_json(SEED_DIR / "events.json", []):
            connection.execute(
                """
                INSERT OR IGNORE INTO events
                    (id, title, date, time, location, description, link)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                """,
                (
                    event.get("id"),
                    event.get("title", ""),
                    event.get("date", ""),
                    event.get("time", "18h00"),
                    event.get("location", "Toulon"),
                    event.get("description", ""),
                    event.get("link"),
                ),
            )

        _set_meta(connection, "events_json_imported")

    if not _meta_exists(connection, "members_json_imported"):
        for member in _read_json(SEED_DIR / "members.json", []):
            connection.execute(
                """
                INSERT OR IGNORE INTO members
                    (id, name, email, phone, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?)
                """,
                (
                    member.get("id"),
                    member.get("name", ""),
                    member.get("email", ""),
                    member.get("phone", ""),
                    member.get("status", "membre"),
                    member.get("created_at", datetime.now().strftime("%Y-%m-%d")),
                ),
            )

        _set_meta(connection, "members_json_imported")


def _read_json(path: Path, fallback: Any) -> Any:
    if not path.exists():
        return fallback
    return json.loads(path.read_text(encoding="utf-8"))


def _meta_exists(connection: sqlite3.Connection, key: str) -> bool:
    return (
        connection.execute("SELECT 1 FROM meta WHERE key = ?", (key,)).fetchone()
        is not None
    )


def _set_meta(connection: sqlite3.Connection, key: str) -> None:
    connection.execute(
        "INSERT OR REPLACE INTO meta (key, value) VALUES (?, ?)",
        (key, datetime.now().astimezone().isoformat()),
    )


def fetch_site_config(connection: sqlite3.Connection) -> dict[str, Any]:
    config: dict[str, Any] = {
        "association": {},
        "social": {},
        "activities": [],
    }

    for row in connection.execute("SELECT section, key, value FROM settings"):
        if row["section"] in config:
            config[row["section"]][row["key"]] = row["value"]

    config["activities"] = [
        dict(row)
        for row in connection.execute(
            """
            SELECT icon, title, description
            FROM activities
            WHERE enabled = 1
            ORDER BY sort_order ASC, id ASC
            """
        )
    ]
    return config


def audit(
    connection: sqlite3.Connection,
    action: str,
    entity_type: str,
    entity_id: int | str | None = None,
    details: dict[str, Any] | None = None,
) -> None:
    connection.execute(
        """
        INSERT INTO audit_logs (action, entity_type, entity_id, details)
        VALUES (?, ?, ?, ?)
        """,
        (
            action,
            entity_type,
            None if entity_id is None else str(entity_id),
            None if details is None else json.dumps(details, ensure_ascii=False),
        ),
    )
