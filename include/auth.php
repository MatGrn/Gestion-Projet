<?php
// Gestion de la session et des droits

// On démarre la session pour savoir qui est connecté
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Jeton de sécurité mis dans chaque formulaire (protection CSRF)
// Il est créé une seule fois par session
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}


// Retourne true si l'utilisateur connecté est administrateur
function estAdmin() {
    if (isset($_SESSION['role']) && $_SESSION['role'] == 'Administrateur') {
        return true;
    }
    return false;
}

// Retourne true si l'utilisateur connecté est modérateur
function estModerateur() {
    if (isset($_SESSION['role']) && $_SESSION['role'] == 'Modérateur') {
        return true;
    }
    return false;
}

// À mettre en haut des pages réservées aux utilisateurs connectés
function verifierConnexion() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['message'] = "Connectez-vous pour accéder à cette page.";
        $_SESSION['type_message'] = 'erreur';
        header('Location: login.php');
        exit;
    }
}

// À mettre en haut des pages réservées aux administrateurs
function verifierAdmin() {
    verifierConnexion();

    if (!estAdmin()) {
        $_SESSION['message'] = "Accès réservé aux administrateurs.";
        $_SESSION['type_message'] = 'erreur';
        header('Location: allTasks.php');
        exit;
    }
}

// À appeler au début du traitement de chaque formulaire envoyé en POST
function verifierJeton() {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        http_response_code(403);
        die('Jeton de sécurité invalide. Rechargez la page et réessayez.');
    }
}
