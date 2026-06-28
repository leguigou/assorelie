#!/bin/bash
# Script de déploiement auto pour ASSORELIE
# Appelé par webhook GitHub ou manuellement
# Usage: ./deploy.sh

set -e

cd "$(dirname "$0")"

echo "🚀 Déploiement ASSORELIE..."

# Pull les dernières modifs
git pull origin main

# Build l'image Docker
docker build -t assorelie:latest .

# Stop & remove ancien conteneur
docker rm -f assorelie 2>/dev/null || true

# Lancer avec labels Traefik
docker run -d \
  --name assorelie \
  --restart unless-stopped \
  --network dokploy-network \
  --env ASSORELIE_DATA_DIR=/var/www/html/data \
  --env ASSORELIE_DB_PATH=/var/www/html/data/assorelie.sqlite \
  --env ASSORELIE_SEED_DIR=/var/www/html/database/seeds \
  --volume /home/guillaume/assorelie/data:/var/www/html/data \
  --label 'traefik.enable=true' \
  --label 'traefik.http.routers.assorelie.rule=Host(`assorelie.deloffre.fr`)' \
  --label 'traefik.http.routers.assorelie.entrypoints=web' \
  --label 'traefik.http.routers.assorelie-secure.rule=Host(`assorelie.deloffre.fr`)' \
  --label 'traefik.http.routers.assorelie-secure.entrypoints=websecure' \
  --label 'traefik.http.routers.assorelie-secure.tls=true' \
  --label 'traefik.http.routers.assorelie-secure.tls.certResolver=letsencrypt' \
  --label 'traefik.http.services.assorelie.loadbalancer.server.port=80' \
  assorelie:latest

sleep 2
echo "✅ Déploiement terminé — $(curl -sL -o /dev/null -w '%{http_code}' https://assorelie.deloffre.fr)"
