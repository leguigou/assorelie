#!/bin/sh
set -eu

DATA_DIR="${ASSORELIE_DATA_DIR:-/app/data}"

mkdir -p "$DATA_DIR"
chown -R www-data:www-data "$DATA_DIR"
chmod 775 "$DATA_DIR"
find "$DATA_DIR" -maxdepth 1 -type f -exec chmod 664 {} \;

exec gosu www-data "$@"
