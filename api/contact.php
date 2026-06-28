<?php
/**
 * API — Formulaire de contact
 * Reçoit les données POST et envoie un email à l'association.
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
  exit;
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation
if (empty($name) || empty($email) || empty($message)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => 'Tous les champs sont requis.']);
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => 'Adresse email invalide.']);
  exit;
}

// Lecture de l'adresse de destination depuis SQLite
$to = assorelie_setting('association', 'email');
if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'error' => "L'adresse de contact de l'association n'est pas configurée."
  ]);
  exit;
}

$subject = "ASSORELIE — Message de $name";
$headers = "From: $email\r\nReply-To: $email\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=utf-8\r\n";
$body = "Nom : $name\nEmail : $email\n\nMessage :\n$message";

$sent = mail($to, $subject, $body, $headers);

if ($sent) {
  echo json_encode(['success' => true, 'message' => 'Message envoyé avec succès.']);
} else {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => "Impossible d'envoyer l'email. Veuillez réessayer ou nous écrire directement à $to."]);
}
