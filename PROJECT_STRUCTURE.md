# ASSORELIE — Landing page

## Stack
- PHP 8+ natif (zéro framework)
- Tailwind CSS v4 (CDN)
- JSON flat-file (pas de BDD)
- AJAX pour sections dynamiques

## Structure

```
assorelie/
├── index.php              # Routeur + page principale
├── .htaccess              # Rewrite + sécurité
├── composer.json          # (optionnel, juste pour infos PHP)
│
├── assets/
│   ├── css/
│   │   └── style.css      # Surcouche CSS si besoin
│   ├── js/
│   │   └── main.js        # AJAX + interactions légères
│   └── images/
│       ├── hero.webp
│       ├── logo.webp
│       └── gallery/       # Photos activités
│
├── includes/
│   ├── header.php         # <head>, nav, ouverture body
│   ├── hero.php           # Section hero
│   ├── activities.php     # Activités (statique)
│   ├── about.php          # À propos / RNA
│   ├── contact.php        # Contact + formulaire
│   └── footer.php         # Footer + scripts
│
├── data/
│   ├── events.json        # Événements à venir
│   └── config.json        # Infos association (email, tel, RNA, réseaux)
│
└── api/
    ├── events.php         # Endpoint AJAX → retourne events.json en JSON
    └── contact.php        # Endpoint AJAX → envoie email
```

## Sections de la page

1. **Hero** — Logo + "L'asso qui relie" + CTA Adhésion
2. **Valeurs** — Les 4 piliers (lien social, savoirs, art/culture, environnement)
3. **Activités** — Grille des activités avec icônes
4. **Agenda** — Prochains événements (chargé en AJAX)
5. **Galerie** — Photos (lazy load natif)
6. **À propos** — RNA, date création, infos légales
7. **Contact** — Formulaire + réseaux sociaux

## Déploiement

1. Développement local
2. Push sur GitHub (repo assorelie)
3. Déploiement Dokploy sur assorelie.deloffre.fr
4. Plus tard : copie vers mutualisé OVH (zéro modif nécessaire)
