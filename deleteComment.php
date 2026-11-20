<?php
require 'include/bdd.php';
require 'include/auth.php';
require 'include/comments.php';

verifierConnexion();

// La suppression se fait seulement avec le bouton (formulaire POST)
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: allTasks.php');
    exit;
}

verifierJeton();

$id = (int) $_POST['id'];
$commentaire = getCommentaire($pdo, $id);

if (!$commentaire) {
    $_SESSION['message'] = 'Commentaire introuvable.';
    $_SESSION['type_message'] = 'erreur';
    header('Location: allTasks.php');
    exit;
}

// L'auteur, l'administrateur ou le modérateur peut supprimer
if (peutSupprimerCommentaire($commentaire)) {
    supprimerCommentaire($pdo, $id);
    $_SESSION['message'] = 'Commentaire supprimé.';
    $_SESSION['type_message'] = 'ok';
} else {
    $_SESSION['message'] = 'Vous ne pouvez pas supprimer ce commentaire.';
    $_SESSION['type_message'] = 'erreur';
}

// Retour à la tâche, au niveau des commentaires
header('Location: task.php?id=' . $commentaire['task_id'] . '#commentaires');
exit;
