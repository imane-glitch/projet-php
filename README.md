# Liste des membres du groupe :
- Imane Iguederzen
- Imen Khlifi
- Sarah Ponnourangame
- Nermine Khadhraoui
- Nazim Bechaoui

Documentation pour l'installation du projet :

### 1. Prérequis

Avant d’installer le projet, assurez-vous d’avoir installé :

- Un serveur web (Apache avec XAMPP, WAMP, LAMP, ou autre)
- PHP 7.4 ou supérieur
- MySQL ou MariaDB pour la base de données
- phpMyAdmin (optionnel, mais recommandé pour gérer la base de données)

### 2. Installation du Projet

Étape 1 : Télécharger le projet

- Téléchargez l’archive ZIP et extrayez-la dans le dossier htdocs de XAMPP ou www de WAMP.

Étape 2 : Configuration de la Base de Données

Créer une base de données dans MySQL :
- Ouvrir phpMyAdmin
- Aller dans l’onglet Bases de données
- Créer une base nommée projet_php
- Importer les fichiers SQL fourni (file_transfer.sql et database.sql) :
- Aller dans phpMyAdmin > projet_php > Importer
- Sélectionner database.sql et cliquer sur Exécuter
- Répéter l’opération pour file_transfer.sql

Étape 3 : Configuration des Identifiants MySQL

- Ouvrir le fichier config.php et modifier les informations de connexion :
$host = 'localhost';
$dbname = 'mon_projet';
$username = 'root'; // Mettre l’utilisateur MySQL
$password = ''; // Mot de passe (laisser vide sur XAMPP)

Étape 4 : Lancer le projet

- Démarrer Apache et MySQL via XAMPP/WAMP
- Ouvrir un navigateur et aller à :
http://localhost/projet_php
- La page d’accueil s’affichera avec les options Inscription, Connexion et Gestion des fichiers.