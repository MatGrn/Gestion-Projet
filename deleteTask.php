<?php
require 'include/bdd.php';
require 'include/auth.php';
require 'include/tasks.php';

verifierConnexion();

// La suppression se fait seulement avec le bouton (formulaire POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verifierJeton();

    $id = (int) $_POST['id'];
    $tache = getTache($pdo, $id);

    if (!$tache) {
        $_SESSION['message'] = 'Tâche introuvable.';
        $_SESSION['type_message'] = 'erreur';
    } else if (!estAdmin() && $tache['user_id'] != $_SESSION['user_id']) {
        // On ne peut supprimer que ses propres tâches (sauf l'administrateur)
        $_SESSION['message'] = 'Vous ne pouvez pas supprimer cette tâche.';
        $_SESSION['type_message'] = 'erreur';
    } else {
        supprimerTache($pdo, $id);
        $_SESSION['message'] = 'Tâche supprimée.';
        $_SESSION['type_message'] = 'ok';
    }
}

header('Location: allTasks.php');
exit;
