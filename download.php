<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_email'])) {
    die("Accès refusé. Connectez-vous d'abord.");
}

$user_email = $_SESSION['user_email'];

if (!isset($_GET['file_id'])) {
    die("Fichier non spécifié.");
}

$file_id = intval($_GET['file_id']);

$stmt = $pdo->prepare("SELECT * FROM fichiers WHERE id = ?");
$stmt->execute([$file_id]);
$file = $stmt->fetch();

if (!$file) {
    die("Fichier introuvable.");
}

if ($file['autorise'] && $file['autorise'] !== $user_email) {
    die("Vous n'avez pas l'autorisation de télécharger ce fichier.");
}

if ($file['user_email'] !== $user_email && !$file['autorise']) {
    die("Vous ne pouvez pas télécharger ce fichier.");
}

$stmt = $pdo->prepare("UPDATE fichiers SET telechargements = telechargements + 1 WHERE id = ?");
$stmt->execute([$file_id]);

$file_path = "uploads/" . md5($file['user_email']) . "/" . $file['nom_stocke'];

if (file_exists($file_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $file['nom_original'] . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit;
} else {
    die("Fichier introuvable.");
}
?>
