<?php
require 'include/bdd.php';
require 'include/auth.php';
require 'include/users.php';
require 'include/tasks.php';

verifierConnexion();

// Récupération de la tâche à modifier
$id = 0;
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
}
$tache = getTache($pdo, $id);

if (!$tache) {
    $_SESSION['message'] = 'Tâche introuvable.';
    $_SESSION['type_message'] = 'erreur';
    header('Location: allTasks.php');
    exit;
}

// On ne peut modifier que ses propres tâches (sauf l'administrateur)
if (!estAdmin() && $tache['user_id'] != $_SESSION['user_id']) {
    $_SESSION['message'] = 'Vous ne pouvez pas modifier cette tâche.';
    $_SESSION['type_message'] = 'erreur';
    header('Location: allTasks.php');
    exit;
}

$categories = getCategories($pdo);

$utilisateurs = [];
if (estAdmin()) {
    $utilisateurs = getUsers($pdo);
}

// Le formulaire est pré-rempli avec les valeurs actuelles de la tâche
$titreTache = $tache['title'];
$description = $tache['description'];
$userIdTache = $tache['user_id'];
$categorieId = $tache['category_id'];
$statut = $tache['status'];
$priorite = $tache['priority'];
$echeance = $tache['due_date'];
$erreurs = [];

// Les champs vides sont NULL dans la base : on les remplace par du texte vide pour le formulaire
if ($description == null) {
    $description = '';
}
if ($echeance == null) {
    $echeance = '';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verifierJeton();

    // Récupération des champs du formulaire
    $titreTache = trim($_POST['title']);
    $description = trim($_POST['description']);
    $categorieId = $_POST['category_id'];
    $statut = $_POST['status'];
    $priorite = (int) $_POST['priority'];
    $echeance = $_POST['due_date'];

    // Seul l'administrateur peut changer la personne à qui la tâche est attribuée
    if (estAdmin()) {
        $userIdTache = (int) $_POST['user_id'];
    }

    // ----- Vérification des champs -----

    if ($titreTache == '' || strlen($titreTache) > 150) {
        $erreurs[] = "Le titre est obligatoire (150 caractères maximum).";
    }
    if (!isset($listeStatuts[$statut])) {
        $erreurs[] = "Le statut choisi n'existe pas.";
    }
    if (!isset($listePriorites[$priorite])) {
        $erreurs[] = "La priorité choisie n'existe pas.";
    }

    if ($categorieId != '') {
        $categorieTrouvee = false;
        foreach ($categories as $c) {
            if ($c['id'] == $categorieId) {
                $categorieTrouvee = true;
            }
        }
        if (!$categorieTrouvee) {
            $erreurs[] = "La catégorie choisie n'existe pas.";
        }
    }

    if ($echeance != '') {
        $morceaux = explode('-', $echeance);
        if (count($morceaux) != 3 || !checkdate((int) $morceaux[1], (int) $morceaux[2], (int) $morceaux[0])) {
            $erreurs[] = "La date d'échéance n'est pas valide.";
        }
    }

    if (estAdmin()) {
        $utilisateurTrouve = false;
        foreach ($utilisateurs as $u) {
            if ($u['id'] == $userIdTache) {
                $utilisateurTrouve = true;
            }
        }
        if (!$utilisateurTrouve) {
            $erreurs[] = "L'utilisateur choisi n'existe pas.";
        }
    }

    // ----- Enregistrement -----

    if (count($erreurs) == 0) {
        $categorieBdd = null;
        if ($categorieId != '') {
            $categorieBdd = (int) $categorieId;
        }
        $descriptionBdd = null;
        if ($description != '') {
            $descriptionBdd = $description;
        }
        $echeanceBdd = null;
        if ($echeance != '') {
            $echeanceBdd = $echeance;
        }

        modifierTache($pdo, $id, $userIdTache, $categorieBdd, $titreTache, $descriptionBdd, $statut, $priorite, $echeanceBdd);

        $_SESSION['message'] = 'Tâche « ' . $titreTache . ' » modifiée.';
        $_SESSION['type_message'] = 'ok';
        header('Location: allTasks.php');
        exit;
    }
}

$titre = 'Modifier la tâche';
$modification = true;
require 'header.php';
require 'include/taskForm.php';
require 'footer.php';
