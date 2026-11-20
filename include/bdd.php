<?php
// Connexion à la base de données avec PDO

$host = '127.0.0.1';        // Adresse du serveur MySQL
$db = 'mon_app';            // Nom de la base
$user = 'root';             // Utilisateur MySQL
$pass = 'root';             // Mot de passe MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);

    // Les erreurs SQL déclenchent une exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Les résultats sont récupérés sous forme de tableau associatif
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Vraies requêtes préparées
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données.");
}
