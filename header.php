<?php
// Haut de page commun à toutes les pages
// La page doit définir $titre avant d'inclure ce fichier

if (!isset($titre)) {
    $titre = 'Gestion de projet';
}

// Nom du fichier de la page actuelle, pour mettre en évidence le bon lien du menu
$page = basename($_SERVER['PHP_SELF']);

$classeUtilisateurs = '';
if ($page == 'index.php' || $page == 'addUser.php' || $page == 'editUser.php') {
    $classeUtilisateurs = 'actif';
}

$classeTaches = '';
if ($page == 'allTasks.php' || $page == 'editTask.php' || $page == 'task.php' || $page == 'editComment.php') {
    $classeTaches = 'actif';
}

$classeNouvelleTache = '';
if ($page == 'addTask.php') {
    $classeNouvelleTache = 'actif';
}

// Pas de menu sur la page de connexion
$classePage = 'page';
if (!isset($_SESSION['user_id'])) {
    $classePage = 'page sans-menu';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titre); ?> - GestionProjet</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- le ?v= change quand le CSS est modifié, pour que le navigateur recharge le fichier -->
    <link rel="stylesheet" href="style.css?v=<?php echo filemtime('style.css'); ?>">
</head>
<body>
<div class="<?php echo $classePage; ?>">

<?php if (isset($_SESSION['user_id'])) { ?>
    <aside class="menu">
        <a class="logo" href="allTasks.php">GestionProjet</a>

        <nav>
            <?php if (estAdmin()) { ?>
                <a href="index.php" class="<?php echo $classeUtilisateurs; ?>">Utilisateurs</a>
            <?php } ?>

            <?php if (estAdmin() || estModerateur()) { ?>
                <a href="allTasks.php" class="<?php echo $classeTaches; ?>">Toutes les tâches</a>
            <?php } else { ?>
                <a href="allTasks.php" class="<?php echo $classeTaches; ?>">Mes tâches</a>
            <?php } ?>

            <a href="addTask.php" class="<?php echo $classeNouvelleTache; ?>">Nouvelle tâche</a>
        </nav>

        <div class="compte">
            <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            <span><?php echo htmlspecialchars($_SESSION['role']); ?></span>
            <form method="post" action="logout.php">
                <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
                <button type="submit" class="btn-deconnexion">Se déconnecter</button>
            </form>
        </div>
    </aside>
<?php } ?>

<main>
    <?php if (!isset($_SESSION['user_id'])) { ?>
        <p class="logo-connexion">GestionProjet</p>
    <?php } ?>

    <h1><?php echo htmlspecialchars($titre); ?></h1>

    <?php
    // Message de confirmation ou d'erreur laissé par la page précédente (affiché une seule fois)
    if (isset($_SESSION['message'])) {
        echo '<p class="flash ' . $_SESSION['type_message'] . '">' . htmlspecialchars($_SESSION['message']) . '</p>';
        unset($_SESSION['message']);
        unset($_SESSION['type_message']);
    }
    ?>
