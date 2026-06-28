<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ASSORELIE — L'asso qui relie | Toulon</title>
  <meta name="description" content="ASSORELIE, l'association qui relie. Activités à Toulon : jeux de société, balades, langue des signes, art, culture, apéros partagés. Rejoignez-nous !">
  <meta name="keywords" content="association, Toulon, Var, lien social, jeux de société, balade, langue des signes, culture, loisirs">
  <meta property="og:title" content="ASSORELIE — L'asso qui relie">
  <meta property="og:description" content="Créer du lien social, partager des savoirs, développer l'art et la culture, sensibiliser à l'environnement à Toulon.">
  <meta property="og:type" content="website">
  <meta property="og:image" content="https://assorelie.deloffre.fr/assets/images/hero-toulon.webp">
  <meta name="theme-color" content="#e85d75">
  <link rel="icon" type="image/webp" href="assets/images/logo-assorelie.webp">
  <link rel="preload" as="image" href="assets/images/hero-toulon.webp" type="image/webp">

  <script>document.documentElement.classList.add('js');</script>
  <link rel="stylesheet" href="assets/css/tailwind.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body class="bg-warm-50 font-inter text-gray-800">
<?php
require_once __DIR__ . '/database.php';

$config = assorelie_site_config();
$asso = $config['association'];
$social = $config['social'];
$activities = $config['activities'];
?>

<header class="site-header">
  <div class="max-w-7xl mx-auto px-5 lg:px-8 h-20 flex items-center justify-between gap-8">
    <a href="#accueil" class="brand-link" aria-label="ASSORELIE — Accueil">
      <img src="assets/images/logo-assorelie.webp" alt="" width="58" height="58" class="brand-logo">
      <span>
        <strong>ASSORELIE</strong>
        <small>L'asso qui relie</small>
      </span>
    </a>

    <nav class="hidden lg:flex items-center gap-7 text-sm font-medium" aria-label="Navigation principale">
      <a href="#accueil" class="nav-link active">Accueil</a>
      <a href="#a-propos" class="nav-link">L'association</a>
      <a href="#activites" class="nav-link">Nos actions</a>
      <a href="#agenda" class="nav-link">Agenda</a>
      <a href="#contact" class="nav-link">Contact</a>
    </nav>

    <a href="<?= htmlspecialchars($social['helloasso']) ?>" target="_blank" rel="noopener"
       class="hidden lg:inline-flex header-cta">
      <span aria-hidden="true">♥</span> Nous soutenir
    </a>

    <button id="menu-btn" type="button" class="lg:hidden menu-button"
            aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="nav-mobile">
      <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="3" y1="12" x2="21" y2="12"/>
        <line x1="3" y1="6" x2="21" y2="6"/>
        <line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
    </button>
  </div>

  <nav id="nav-mobile" class="nav-mobile lg:hidden" aria-label="Navigation mobile">
    <div class="px-6 pb-5 flex flex-col gap-1">
      <a href="#accueil">Accueil</a>
      <a href="#a-propos">L'association</a>
      <a href="#activites">Nos actions</a>
      <a href="#agenda">Agenda</a>
      <a href="#contact">Contact</a>
      <a href="<?= htmlspecialchars($social['helloasso']) ?>" target="_blank" rel="noopener" class="mobile-support">
        ♥ Nous soutenir
      </a>
    </div>
  </nav>
</header>
