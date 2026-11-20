<?php
require 'include/bdd.php';
require 'include/auth.php';
require 'include/users.php';

verifierAdmin();

// Récupération de l'utilisateur à modifier
$id = 0;
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
}
$utilisateur = getUser($pdo, $id);

if (!$utilisateur) {
    $_SESSION['message'] = 'Utilisateur introuvable.';
    $_SESSION['type_message'] = 'erreur';
    header('Location: index.php');
    exit;
}

$roles = getRoles($pdo);

// Le formulaire est pré-rempli avec les valeurs actuelles
$username = $utilisateur['username'];
$email = $utilisateur['email'];
$roleId = $utilisateur['role_id'];
$erreurs = [];

// L'administrateur est-il en train de modifier son propre compte ?
$estMoi = false;
if ($id == $_SESSION['user_id']) {
    $estMoi = true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verifierJeton();

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
    // Le mot de passe n'est vérifié que s'il a été rempli
    if ($motDePasse != '' && strlen($motDePasse) < 8) {
        $erreurs[] = "Le mot de passe doit faire au moins 8 caractères.";
    }

    $roleTrouve = false;
    foreach ($roles as $role) {
        if ($role['id'] == $roleId) {
            $roleTrouve = true;
        }
    }
    if (!$roleTrouve) {
        $erreurs[] = "Le rôle choisi n'existe pas.";
    }

    // Un administrateur ne peut pas changer son propre rôle (il perdrait l'accès à cette page)
    if ($estMoi && $roleId != $utilisateur['role_id']) {
        $erreurs[] = "Vous ne pouvez pas modifier votre propre rôle.";
    }

    if (count($erreurs) == 0 && existeDeja($pdo, $username, $email, $id)) {
        $erreurs[] = "Ce nom d'utilisateur ou cet email existe déjà.";
    }

    if (count($erreurs) == 0) {
        modifierUser($pdo, $id, $username, $email, $roleId);

        // Mot de passe changé seulement s'il a été rempli
        if ($motDePasse != '') {
            modifierMotDePasse($pdo, $id, $motDePasse);
        }

        // Si l'admin a changé son propre nom, on met à jour celui affiché dans le menu
        if ($estMoi) {
            $_SESSION['username'] = $username;
        }

        $_SESSION['message'] = 'Utilisateur « ' . $username . ' » modifié.';
        $_SESSION['type_message'] = 'ok';
        header('Location: index.php');
        exit;
    }
}

$titre = 'Modifier ' . $utilisateur['username'];
$modification = true;
require 'header.php';
require 'include/userForm.php';
require 'footer.php';
