<?php
/**
 * API — Formulaire de contact
 * Reçoit les données POST et envoie un email à l'association.
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

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

// Lecture de la config pour l'email de destination
$config = json_decode(file_get_contents(__DIR__ . '/../data/config.json'), true);
$to = $config['association']['email'];

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
