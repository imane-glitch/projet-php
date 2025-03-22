<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_email'])) {
    die("Accès refusé.");
}

$user_email = $_SESSION['user_email'];

$stmt = $pdo->prepare("SELECT * FROM fichiers WHERE user_email = ?");
$stmt->execute([$user_email]);
$files = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes fichiers</title>
</head>
<body>
    <h1>Mes fichiers</h1>
    <table border="1">
        <tr>
            <th>Nom du fichier</th>
            <th>Téléchargements</th>
            <th>Lien de téléchargement</th>
            <th>Supprimer</th>
        </tr>
        <?php foreach ($files as $file) : ?>
            <tr>
                <td><?= htmlspecialchars($file['nom_original']) ?></td>
                <td><?= $file['telechargements'] ?></td>
                <td><a href="download.php?file_id=<?= $file['id'] ?>">Télécharger</a></td>
                <td><a href="delete.php?file_id=<?= $file['id'] ?>" onclick="return confirm('Supprimer ce fichier ?')"></a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
