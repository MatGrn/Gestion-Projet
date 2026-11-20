<?php
// Fonctions qui travaillent sur la table comments


// Récupère tous les commentaires d'une tâche (réponses comprises), du plus ancien au plus récent
function getCommentaires($pdo, $taskId) {
    $sql = "SELECT comments.*, users.username
            FROM comments
            LEFT JOIN users ON comments.user_id = users.id
            WHERE comments.task_id = :task_id
            ORDER BY comments.created_at ASC, comments.id ASC";
    $requete = $pdo->prepare($sql);
    $requete->execute(['task_id' => $taskId]);
    return $requete->fetchAll();
}

// Récupère un commentaire grâce à son id
function getCommentaire($pdo, $id) {
    $requete = $pdo->prepare("SELECT * FROM comments WHERE id = :id");
    $requete->execute(['id' => $id]);
    return $requete->fetch();
}

// Ajoute un commentaire ($parentId = null pour un commentaire normal, sinon id du commentaire auquel on répond)
// Retourne l'id du nouveau commentaire
function ajouterCommentaire($pdo, $taskId, $userId, $parentId, $contenu) {
    $sql = "INSERT INTO comments (task_id, user_id, parent_id, content)
            VALUES (:task_id, :user_id, :parent_id, :content)";
    $requete = $pdo->prepare($sql);
    $requete->execute([
        'task_id'   => $taskId,
        'user_id'   => $userId,
        'parent_id' => $parentId,
        'content'   => $contenu
    ]);
    return $pdo->lastInsertId();
}

// Modifie le texte d'un commentaire et enregistre la date de modification
function modifierCommentaire($pdo, $id, $contenu) {
    $requete = $pdo->prepare("UPDATE comments SET content = :content, updated_at = NOW() WHERE id = :id");
    $requete->execute(['content' => $contenu, 'id' => $id]);
}

// Supprime un commentaire (ses réponses sont supprimées automatiquement par la base)
function supprimerCommentaire($pdo, $id) {
    $requete = $pdo->prepare("DELETE FROM comments WHERE id = :id");
    $requete->execute(['id' => $id]);
}

// Vérifie le texte d'un commentaire, retourne un message d'erreur ou '' si tout va bien
function verifierCommentaire($contenu) {
    if ($contenu == '') {
        return "Le commentaire ne peut pas être vide.";
    }
    if (mb_strlen($contenu) > 2000) {
        return "Le commentaire ne doit pas dépasser 2000 caractères.";
    }
    return '';
}

// Retourne true si l'utilisateur connecté est l'auteur du commentaire
function estAuteur($commentaire) {
    if ($commentaire['user_id'] != null && $commentaire['user_id'] == $_SESSION['user_id']) {
        return true;
    }
    return false;
}

// L'auteur, un administrateur ou un modérateur peut supprimer un commentaire
function peutSupprimerCommentaire($commentaire) {
    if (estAuteur($commentaire) || estAdmin() || estModerateur()) {
        return true;
    }
    return false;
}

// Affiche l'auteur, la date et le texte d'un commentaire
function afficherCommentaire($commentaire) {
    // Si le compte de l'auteur a été supprimé, on n'a plus son nom
    if ($commentaire['username'] == null) {
        $auteur = 'Utilisateur supprimé';
    } else {
        $auteur = htmlspecialchars($commentaire['username']);
    }

    echo '<p class="commentaire-entete">';
    echo '<strong>' . $auteur . '</strong> le ' . date('d/m/Y à H:i', strtotime($commentaire['created_at']));
    if ($commentaire['updated_at'] != null) {
        echo ' <span class="modifie">(modifié)</span>';
    }
    echo '</p>';

    echo '<p class="commentaire-texte">' . nl2br(htmlspecialchars($commentaire['content'])) . '</p>';
}
