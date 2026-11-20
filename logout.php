<?php
require 'include/auth.php';

// La déconnexion se fait seulement avec le bouton (formulaire POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verifierJeton();

    // On vide puis on détruit la session
    $_SESSION = [];
    session_destroy();

    // Nouvelle session vide, juste pour afficher le message sur la page de connexion
    session_start();
    $_SESSION['message'] = 'Vous êtes déconnecté.';
    $_SESSION['type_message'] = 'ok';
}

header('Location: login.php');
exit;
