<?php
require 'include/bdd.php';
require 'include/auth.php';
require 'include/users.php';
require 'include/tasks.php';

verifierConnexion();

$categories = getCategories($pdo);

// La liste des utilisateurs ne sert qu'à l'administrateur (champ "Attribuée à")
$utilisateurs = [];
if (estAdmin()) {
    $utilisateurs = getUsers($pdo);
}

// Valeurs par défaut : la tâche est pour l'utilisateur connecté, à faire, priorité normale
$titreTache = '';
$description = '';
$userIdTache = $_SESSION['user_id'];
$categorieId = '';
$statut = 'todo';
$priorite = 2;
$echeance = '';
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verifierJeton();

    // Récupération des champs du formulaire
    $titreTache = trim($_POST['title']);
    $description = trim($_POST['description']);
    $categorieId = $_POST['category_id'];
    $statut = $_POST['status'];
    $priorite = (int) $_POST['priority'];
    $echeance = $_POST['due_date'];

    // Seul l'administrateur peut attribuer la tâche à quelqu'un d'autre
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

    // La catégorie est facultative, mais si elle est choisie elle doit exister
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

    // La date est facultative, mais si elle est remplie elle doit être valide (format AAAA-MM-JJ)
    if ($echeance != '') {
        $morceaux = explode('-', $echeance);
        if (count($morceaux) != 3 || !checkdate((int) $morceaux[1], (int) $morceaux[2], (int) $morceaux[0])) {
            $erreurs[] = "La date d'échéance n'est pas valide.";
        }
    }

    // L'utilisateur choisi par l'administrateur doit exister
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
        // Les champs facultatifs vides sont enregistrés comme NULL
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

        ajouterTache($pdo, $userIdTache, $categorieBdd, $titreTache, $descriptionBdd, $statut, $priorite, $echeanceBdd);

        $_SESSION['message'] = 'Tâche « ' . $titreTache . ' » ajoutée.';
        $_SESSION['type_message'] = 'ok';
        header('Location: allTasks.php');
        exit;
    }
}

$titre = 'Nouvelle tâche';
$modification = false;
require 'header.php';
require 'include/taskForm.php';
require 'footer.php';
