<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_email'])) {
    die("Accès refusé. Connectez-vous d'abord.");
}

$user_email = $_SESSION['user_email'];
$user_hash = md5($user_email); 
$upload_dir = "uploads/$user_hash/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] != 0) {
    die("Erreur lors de l'upload.");
}

$file = $_FILES['file'];
$original_name = basename($file['name']);
$file_ext = pathinfo($original_name, PATHINFO_EXTENSION);
$file_size = $file['size'];

$forbidden_extensions = ['php', 'exe', 'sh', 'bat'];
if (in_array(strtolower($file_ext), $forbidden_extensions)) {
    die("Format interdit.");
}

if ($file_size > 20 * 1024 * 1024) {
    die("Fichier trop volumineux (max 20 Mo).");
}

$new_name = uniqid() . "." . $file_ext;
$file_path = $upload_dir . $new_name;

if (move_uploaded_file($file['tmp_name'], $file_path)) {
    $autorise = isset($_POST['autorise']) ? trim($_POST['autorise']) : null;

    $stmt = $pdo->prepare("INSERT INTO fichiers (nom_original, nom_stocke, user_email, telechargements, autorise) VALUES (?, ?, ?, 0, ?)");
    $stmt->execute([$original_name, $new_name, $user_email, $autorise]);

    echo "Fichier envoyé avec succès.";
} else {
    echo "Erreur lors de l'envoi.";
}
?>

