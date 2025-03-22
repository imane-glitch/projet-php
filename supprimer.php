<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_email'])) {
    die("Accès refusé.");
}

$user_email = $_SESSION['user_email'];

if (!isset($_GET['file_id'])) {
    die("Fichier non spécifié.");
}

$file_id = intval($_GET['file_id']);

$stmt = $pdo->prepare("SELECT * FROM fichiers WHERE id = ? AND user_email = ?");
$stmt->execute([$file_id, $user_email]);
$file = $stmt->fetch();

if (!$file) {
    die("Fichier introuvable.");
}

$file_path = "uploads/" . md5($user_email) . "/" . $file['nom_stocke'];

if (file_exists($file_path)) {
    unlink($file_path);
}

$stmt = $pdo->prepare("DELETE FROM fichiers WHERE id = ?");
$stmt->execute([$file_id]);

header("Location: my_files.php");
exit;
?>
