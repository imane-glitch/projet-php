<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Utilisateur</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
require 'config.php';
session_start();


// Gestion de l'inscription
if (isset($_POST["register"])) {
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $fullname = $_POST["fullname"];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "Cet email est déjà utilisé.";
        exit;
}

    $stmt = $pdo->prepare("INSERT INTO users (email, password, fullname) VALUES (?, ?, ?)");
    if ($stmt->execute([$email, $password, $fullname])) {
        echo "Compte créé avec succès !";
    } else {
        echo "Erreur lors de l'inscription.";
    }
}

// Gestion de la connexion
if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["fullname"] = $user["fullname"];
        echo "Connexion réussie !";
    } else {
        echo "Identifiants incorrects.";
    }
}


// Gestion de la modification du profil
if (isset($_POST["update"])) {
    $newFullname = $_POST["fullname"];
    $stmt = $pdo->prepare("UPDATE users SET fullname = ? WHERE id = ?");
    $stmt->execute([$newFullname, $_SESSION["user_id"]]);
    $_SESSION["fullname"] = $newFullname;
    echo "Profil mis à jour !";
}

// Gestion de la déconnexion
if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>

<!-- Formulaire d'inscription -->
<h2>Créer un compte</h2>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <input id="em" type="email" name="email" placeholder="E-mail" required>
    <input type="password" name="password" placeholder="Mot de passe" minlength="6" required>
    <input type="text" name="fullname" placeholder="Nom complet">
    <input type="submit" name="register" value="S'inscrire">
</form>

<!-- Formulaire de connexion -->
<h2>Se connecter</h2>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <input id="em" type="email" name="email" placeholder="E-mail" required>
    <input id="pass" type="password" name="password" placeholder="Mot de passe" required>
    <input type="submit" name="login" value="Se connecter">
</form>

<!-- Formulaire de modification de profil -->
<?php if (isset($_SESSION["user_id"])): ?>
    <h2>Modifier le profil</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <input type="text" name="fullname" placeholder="Nouveau nom" value="<?= $_SESSION["fullname"] ?>">
        <input type="submit" name="update" value="Mettre à jour">
    </form>
    
    <!-- Bouton de déconnexion -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <input type="submit" name="logout" value="Se déconnecter">
    </form>
<?php endif; ?>

<form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <input type="email" name="autorise" placeholder="Autoriser un utilisateur (email)">
    <button type="submit">Envoyer</button>
</form>


<h1>Mes fichiers</h1>
<?php
require 'db.php';
 
$stmt = $pdo->prepare("SELECT * FROM fichiers WHERE user_email = ?");
$stmt->execute([$user_email]);
$files = $stmt->fetchAll();
?>
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
                <td><a href="<?= $file['lien_telechargement'] ?>">Lien</a></td>
                <td><?= $file['telechargements'] ?></td>
                <td><a href="download.php?file_id=<?= $file['id'] ?>">Télécharger</a></td>
                <td><a href="delete.php?file_id=<?= $file['id'] ?>" onclick="return confirm('Supprimer ce fichier ?')">Supprimer</a></td>
            </tr>
        <?php endforeach; ?>
    </table>


<!-- DOWNLOAD -->

<?php
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



<!-- UPLOAD -->

<?php
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
    $autorise = !empty($_POST['autorise']) ? trim($_POST['autorise']) : null;

    $stmt = $pdo->prepare("INSERT INTO fichiers (nom_original, nom_stocke, user_email, telechargements, autorise) VALUES (?, ?, ?, 0, ?)");
    $stmt->execute([$original_name, $new_name, $user_email, $autorise]);

    echo "Fichier envoyé avec succès.";
} else {
    echo "Erreur lors de l'envoi.";
}
?>


<!-- SUPPRIMER UN FICHIER -->

<?php
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

<?php $lien_telechargement = "telechargement.php?cle=" . bin2hex(random_bytes(5)); 
$stmt = $pdo->prepare("UPDATE fichiers SET lien_telechargement = ? WHERE id = ?");
$stmt->execute([$lien_telechargement, $file_id]);
?>


</body>
</html>

