<?php
require 'include/bdd.php';
require 'include/auth.php';
require 'include/users.php';

verifierAdmin();

$roles = getRoles($pdo);

// Valeurs par défaut du formulaire (rôle "Utilisateur")
$username = '';
$email = '';
$roleId = 2;
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verifierJeton();

    // Récupération des champs du formulaire
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $roleId = (int) $_POST['role_id'];
    $motDePasse = $_POST['password'];

    // Vérification des champs
    if (strlen($username) < 3 || strlen($username) > 50) {
        $erreurs[] = "Le nom d'utilisateur doit faire entre 3 et 50 caractères.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $erreurs[] = "L'adresse email n'est pas valide.";
    }
    if (strlen($motDePasse) < 8) {
        $erreurs[] = "Le mot de passe doit faire au moins 8 caractères.";
    }

    // Le rôle doit exister dans la table roles
    $roleTrouve = false;
    foreach ($roles as $role) {
        if ($role['id'] == $roleId) {
            $roleTrouve = true;
        }
    }
    if (!$roleTrouve) {
        $erreurs[] = "Le rôle choisi n'existe pas.";
    }

    // Le nom et l'email ne doivent pas être déjà utilisés
    if (count($erreurs) == 0 && existeDeja($pdo, $username, $email, 0)) {
        $erreurs[] = "Ce nom d'utilisateur ou cet email existe déjà.";
    }

    // Aucune erreur : on enregistre
    if (count($erreurs) == 0) {
        ajouterUser($pdo, $username, $email, $motDePasse, $roleId);

        $_SESSION['message'] = 'Utilisateur « ' . $username . ' » ajouté.';
        $_SESSION['type_message'] = 'ok';
        header('Location: index.php');
        exit;
    }
}

$titre = 'Ajouter un utilisateur';
$modification = false;
require 'header.php';
require 'include/userForm.php';
require 'footer.php';
