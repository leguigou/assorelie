<?php
/**
 * API — Liste des événements
 * Retourne le contenu de data/events.json au format JSON.
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: max-age=300, public');

$file = __DIR__ . '/../data/events.json';

if (!file_exists($file)) {
  http_response_code(500);
  echo json_encode(['error' => 'Fichier de données introuvable']);
  exit;
}

$events = json_decode(file_get_contents($file), true);

if ($events === null) {
  http_response_code(500);
  echo json_encode(['error' => 'Erreur de lecture des données']);
  exit;
}

// Ne pas filtrer : on garde tout, passé et futur
$today = date('Y-m-d');

// Ajouter le flag 'past' et trier par date croissante
foreach ($events as &$e) {
  $e['past'] = $e['date'] < $today;
}
unset($e);

usort($events, fn($a, $b) => strcmp($a['date'], $b['date']));

echo json_encode(array_values($events), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
