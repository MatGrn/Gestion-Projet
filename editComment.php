<?php
require 'include/bdd.php';
require 'include/auth.php';
require 'include/comments.php';

verifierConnexion();

// Récupération du commentaire à modifier
$id = 0;
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
}
$commentaire = getCommentaire($pdo, $id);

if (!$commentaire) {
    $_SESSION['message'] = 'Commentaire introuvable.';
    $_SESSION['type_message'] = 'erreur';
    header('Location: allTasks.php');
    exit;
}

// Seul l'auteur peut modifier son commentaire
if (!estAuteur($commentaire)) {
    $_SESSION['message'] = 'Vous ne pouvez modifier que vos propres commentaires.';
    $_SESSION['type_message'] = 'erreur';
    header('Location: task.php?id=' . $commentaire['task_id']);
    exit;
}

$contenu = $commentaire['content'];
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verifierJeton();

    $contenu = trim($_POST['content']);
    $erreur = verifierCommentaire($contenu);

    if ($erreur == '') {
        modifierCommentaire($pdo, $id, $contenu);

        $_SESSION['message'] = 'Commentaire modifié.';
        $_SESSION['type_message'] = 'ok';
        header('Location: task.php?id=' . $commentaire['task_id'] . '#com-' . $id);
        exit;
    }
}

$titre = 'Modifier le commentaire';
require 'header.php';
?>

<?php if ($erreur != '') { ?>
    <p class="flash erreur"><?php echo $erreur; ?></p>
<?php } ?>

<form method="post" class="formulaire">
    <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">

    <label for="content">Commentaire</label>
    <textarea id="content" name="content" rows="6" maxlength="2000" required><?php echo htmlspecialchars($contenu); ?></textarea>

    <p>
        <button type="submit" class="btn">Enregistrer</button>
        <a href="task.php?id=<?php echo $commentaire['task_id']; ?>#com-<?php echo $id; ?>">Annuler</a>
    </p>
</form>

<?php require 'footer.php'; ?>
