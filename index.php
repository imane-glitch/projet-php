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

</body>
</html>
