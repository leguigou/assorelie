<?php
/**
 * API — Liste des événements
 * Retourne les événements enregistrés dans SQLite.
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: max-age=300, public');

require_once __DIR__ . '/../includes/database.php';

try {
  $today = date('Y-m-d');
  $statement = assorelie_db()->prepare(
    'SELECT id, title, date, time, location, description, link, image, image_alt,
            CASE WHEN date < :today THEN 1 ELSE 0 END AS past
     FROM events
     ORDER BY
       CASE WHEN date < :today THEN 1 ELSE 0 END ASC,
       CASE WHEN date >= :today THEN date END ASC,
       CASE WHEN date < :today THEN date END DESC'
  );
  $statement->execute([':today' => $today]);
  $events = $statement->fetchAll();

  foreach ($events as &$event) {
    $event['id'] = (int) $event['id'];
    $event['past'] = (bool) $event['past'];
  }
  unset($event);

  echo json_encode($events, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Throwable $error) {
  http_response_code(500);
  echo json_encode(
    ['error' => 'Impossible de charger les événements.'],
    JSON_UNESCAPED_UNICODE
  );
}
