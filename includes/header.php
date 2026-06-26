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
  <meta name="theme-color" content="#e85d75">

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            rose: {
              50: '#fff1f2',
              100: '#ffe4e6',
              200: '#fecdd3',
              300: '#fda4af',
              400: '#e85d75',
              500: '#d14860',
              600: '#b13c51',
            },
            warm: {
              50: '#fff8f5',
              100: '#fef0ec',
              200: '#fde0d8',
            }
          },
          fontFamily: {
            quicksand: ['Quicksand', 'sans-serif'],
            inter: ['Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body class="bg-warm-50 font-inter text-gray-800">
<?php
$config = json_decode(file_get_contents(__DIR__ . '/../data/config.json'), true);
$asso = $config['association'];
$social = $config['social'];
$activities = $config['activities'];
?>
