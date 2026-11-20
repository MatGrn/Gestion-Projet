<?php
require 'include/bdd.php';
require 'include/auth.php';
require 'include/users.php';

verifierAdmin();

// La suppression se fait seulement avec le bouton (formulaire POST), pas avec un simple lien
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verifierJeton();

    $id = (int) $_POST['id'];

    if ($id == $_SESSION['user_id']) {
        $_SESSION['message'] = 'Vous ne pouvez pas supprimer votre propre compte.';
        $_SESSION['type_message'] = 'erreur';
    } else if (supprimerUser($pdo, $id)) {
        $_SESSION['message'] = 'Utilisateur supprimé.';
        $_SESSION['type_message'] = 'ok';
    } else {
        $_SESSION['message'] = 'Utilisateur introuvable.';
        $_SESSION['type_message'] = 'erreur';
    }
}

header('Location: index.php');
exit;
