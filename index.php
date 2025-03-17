<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form action=<?php echo $_SERVER['PHP_SELF'];?> method=post>
        <input id="em" type="email" name="adelec" placeholder="E-mail">
        <input type=submit value="Envoyer">
    </form>
    <?php
    var_dump($_POST['adelec']);
    ?>
    
    <form action=<?php echo $_SERVER['PHP_SELF'];?> method=post>
        <input id="pass" type="password" name="pw" placeholder="Mot de Passe" size=10 minlength=6 autocomplete="off" required>
        <input type=submit value="Envoyer">
    </form> 
    <?php
    var_dump($_POST['pw']);
    ?><br>

<a href="App.php">Se connecter</a>

</body>
</html> 