<?php
require 'include/bdd.php';
require 'include/auth.php';
require 'include/tasks.php';
require 'include/comments.php';

verifierConnexion();

// Récupération de la tâche
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

// Un utilisateur simple ne peut voir que ses propres tâches
if (!estAdmin() && !estModerateur() && $tache['user_id'] != $_SESSION['user_id']) {
    $_SESSION['message'] = "Vous n'avez pas accès à cette tâche.";
    $_SESSION['type_message'] = 'erreur';
    header('Location: allTasks.php');
    exit;
}

// Id du commentaire auquel on est en train de répondre (0 = aucun)
// On clique sur "Répondre" : l'id arrive dans l'URL (task.php?id=5&repondre=12)
$repondre = 0;
if (isset($_GET['repondre'])) {
    $repondre = (int) $_GET['repondre'];
}

// Texte et erreur du formulaire "Ajouter un commentaire"
$contenu = '';
$erreur = '';

// Texte et erreur du formulaire de réponse
$contenuReponse = '';
$erreurReponse = '';


// ----- Envoi d'un commentaire ou d'une réponse -----

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verifierJeton();

    $texte = trim($_POST['content']);

    // S'il y a un parent_id, c'est une réponse à un commentaire
    $parentId = null;
    if (isset($_POST['parent_id']) && $_POST['parent_id'] != '') {
        $parent = getCommentaire($pdo, (int) $_POST['parent_id']);

        // Le commentaire doit exister et appartenir à cette tâche
        if (!$parent || $parent['task_id'] != $id) {
            $_SESSION['message'] = "Le commentaire auquel vous répondez n'existe plus.";
            $_SESSION['type_message'] = 'erreur';
            header('Location: task.php?id=' . $id . '#commentaires');
            exit;
        }

        // Les réponses sont toujours rangées sous le commentaire principal
        // (si on répond à une réponse, on prend le commentaire au-dessus)
        if ($parent['parent_id'] != null) {
            $parentId = $parent['parent_id'];
        } else {
            $parentId = $parent['id'];
        }
    }

    $erreurTexte = verifierCommentaire($texte);

    if ($erreurTexte == '') {
        $nouvelId = ajouterCommentaire($pdo, $id, $_SESSION['user_id'], $parentId, $texte);

        if ($parentId == null) {
            $_SESSION['message'] = 'Commentaire ajouté.';
        } else {
            $_SESSION['message'] = 'Réponse ajoutée.';
        }
        $_SESSION['type_message'] = 'ok';

        // On revient sur la page, directement au niveau du nouveau commentaire
        header('Location: task.php?id=' . $id . '#com-' . $nouvelId);
        exit;
    }

    // Il y a une erreur : on la réaffiche dans le bon formulaire
    if ($parentId == null) {
        $erreur = $erreurTexte;
        $contenu = $texte;
    } else {
        $erreurReponse = $erreurTexte;
        $contenuReponse = $texte;
        $repondre = $parentId;
    }
}

// Tous les commentaires de la tâche (réponses comprises)
$commentaires = getCommentaires($pdo, $id);

$titre = $tache['title'];
require 'header.php';
?>

<!-- Informations de la tâche -->
<div class="fiche">
    <dl>
        <dt>Attribuée à</dt>
        <dd><?php echo htmlspecialchars($tache['username']); ?></dd>

        <dt>Catégorie</dt>
        <dd><?php if ($tache['categorie'] != null) { echo htmlspecialchars($tache['categorie']); } else { echo 'Aucune'; } ?></dd>

        <dt>Statut</dt>
        <dd><span class="badge <?php echo $tache['status']; ?>"><?php echo $listeStatuts[$tache['status']]; ?></span></dd>

        <dt>Priorité</dt>
        <dd><span class="prio p<?php echo $tache['priority']; ?>"><?php echo $listePriorites[$tache['priority']]; ?></span></dd>

        <dt>Créée le</dt>
        <dd><?php echo date('d/m/Y', strtotime($tache['created_at'])); ?></dd>

        <dt>Échéance</dt>
        <dd><?php if ($tache['due_date'] != null) { echo date('d/m/Y', strtotime($tache['due_date'])); } else { echo '-'; } ?></dd>
    </dl>

    <?php if ($tache['description'] != '') { ?>
        <p class="description"><?php echo nl2br(htmlspecialchars($tache['description'])); ?></p>
    <?php } ?>

    <p class="fiche-actions">
        <?php if (estAdmin() || $tache['user_id'] == $_SESSION['user_id']) { ?>
            <a class="btn" href="editTask.php?id=<?php echo $tache['id']; ?>">Modifier la tâche</a>
        <?php } ?>
        <a href="allTasks.php">Retour à la liste</a>
    </p>
</div>


<!-- Commentaires -->
<h2 id="commentaires">Commentaires (<?php echo count($commentaires); ?>)</h2>

<?php if (count($commentaires) == 0) { ?>
    <p class="vide">Aucun commentaire pour le moment.</p>
<?php } ?>

<?php foreach ($commentaires as $c) {

    // Les réponses ne sont pas affichées ici, mais sous leur commentaire (plus bas)
    if ($c['parent_id'] != null) {
        continue;
    }

    // On compte les réponses de ce commentaire
    $nbReponses = 0;
    foreach ($commentaires as $r) {
        if ($r['parent_id'] == $c['id']) {
            $nbReponses++;
        }
    }

    // Lien du bouton "Répondre"
    $lienRepondre = 'task.php?id=' . $id . '&repondre=' . $c['id'] . '#repondre-' . $c['id'];
?>
    <div class="commentaire" id="com-<?php echo $c['id']; ?>">

        <?php afficherCommentaire($c); ?>

        <div class="commentaire-actions">
            <a href="<?php echo $lienRepondre; ?>">Répondre</a>

            <?php if (estAuteur($c)) { ?>
                <a href="editComment.php?id=<?php echo $c['id']; ?>">Modifier</a>
            <?php } ?>

            <?php if (peutSupprimerCommentaire($c)) {
                // Message de confirmation : on prévient si les réponses vont partir avec
                if ($nbReponses > 0) {
                    $confirmation = 'Supprimer ce commentaire et ses réponses ?';
                } else {
                    $confirmation = 'Supprimer ce commentaire ?';
                }
            ?>
                <form method="post" action="deleteComment.php" class="inline" onsubmit="return confirm('<?php echo $confirmation; ?>');">
                    <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
                    <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                    <button type="submit" class="lien-danger">Supprimer</button>
                </form>
            <?php } ?>
        </div>

        <?php // ----- Réponses de ce commentaire -----
        foreach ($commentaires as $r) {
            if ($r['parent_id'] != $c['id']) {
                continue;
            }
        ?>
            <div class="commentaire" id="com-<?php echo $r['id']; ?>">

                <?php afficherCommentaire($r); ?>

                <div class="commentaire-actions">
                    <a href="<?php echo $lienRepondre; ?>">Répondre</a>

                    <?php if (estAuteur($r)) { ?>
                        <a href="editComment.php?id=<?php echo $r['id']; ?>">Modifier</a>
                    <?php } ?>

                    <?php if (peutSupprimerCommentaire($r)) { ?>
                        <form method="post" action="deleteComment.php" class="inline" onsubmit="return confirm('Supprimer cette réponse ?');">
                            <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
                            <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                            <button type="submit" class="lien-danger">Supprimer</button>
                        </form>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <?php // ----- Formulaire de réponse (seulement sous le commentaire choisi) -----
        if ($repondre == $c['id']) {
            if ($c['username'] == null) {
                $nomAuteur = 'Utilisateur supprimé';
            } else {
                $nomAuteur = $c['username'];
            }
        ?>
            <?php if ($erreurReponse != '') { ?>
                <p class="flash erreur"><?php echo $erreurReponse; ?></p>
            <?php } ?>

            <form method="post" action="task.php?id=<?php echo $id; ?>" class="formulaire formulaire-commentaire" id="repondre-<?php echo $c['id']; ?>">
                <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
                <input type="hidden" name="parent_id" value="<?php echo $c['id']; ?>">

                <label for="reponse">Répondre à <?php echo htmlspecialchars($nomAuteur); ?></label>
                <textarea id="reponse" name="content" rows="3" maxlength="2000" required autofocus><?php echo htmlspecialchars($contenuReponse); ?></textarea>

                <p>
                    <button type="submit" class="btn">Publier la réponse</button>
                    <a href="task.php?id=<?php echo $id; ?>#com-<?php echo $c['id']; ?>">Annuler</a>
                </p>
            </form>
        <?php } ?>

    </div>
<?php } ?>


<!-- Nouveau commentaire -->
<?php if ($erreur != '') { ?>
    <p class="flash erreur"><?php echo $erreur; ?></p>
<?php } ?>

<form method="post" action="task.php?id=<?php echo $id; ?>" class="formulaire formulaire-commentaire">
    <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">

    <label for="content">Ajouter un commentaire</label>
    <textarea id="content" name="content" rows="4" maxlength="2000" required><?php echo htmlspecialchars($contenu); ?></textarea>

    <p><button type="submit" class="btn">Publier</button></p>
</form>

<?php require 'footer.php'; ?>
