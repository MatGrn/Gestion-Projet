<?php
require 'include/bdd.php';
require 'include/auth.php';
require 'include/users.php';
require 'include/tasks.php';

verifierConnexion();


// ----- Lecture des filtres envoyés dans l'URL -----

// Statut : on le garde seulement s'il fait partie de la liste des statuts
$statut = '';
if (isset($_GET['status']) && in_array($_GET['status'], array_keys($listeStatuts), true)) {
    $statut = $_GET['status'];
}

// Priorité : pareil avec la liste des priorités
$prio = 0;
if (isset($_GET['priority']) && isset($listePriorites[(int) $_GET['priority']])) {
    $prio = (int) $_GET['priority'];
}

// Dates de création : on les garde seulement si ce sont de vraies dates (format AAAA-MM-JJ)
$dateDebut = '';
if (isset($_GET['date_debut']) && dateValide($_GET['date_debut'])) {
    $dateDebut = $_GET['date_debut'];
}
$dateFin = '';
if (isset($_GET['date_fin']) && dateValide($_GET['date_fin'])) {
    $dateFin = $_GET['date_fin'];
}

// La date de début ne doit pas être après la date de fin
$erreurDates = '';
if ($dateDebut != '' && $dateFin != '' && $dateDebut > $dateFin) {
    $erreurDates = 'La date de début doit être avant la date de fin.';
    $dateFin = '';
}

// L'administrateur et le modérateur voient les tâches de tout le monde
$voirTout = false;
if (estAdmin() || estModerateur()) {
    $voirTout = true;
}

if ($voirTout) {
    // Ils peuvent filtrer sur un utilisateur
    $utilisateurs = getUsers($pdo);
    $userId = 0;
    if (isset($_GET['user_id'])) {
        $userId = (int) $_GET['user_id'];
    }

    $titre = 'Toutes les tâches';
    foreach ($utilisateurs as $u) {
        if ($u['id'] == $userId) {
            $titre = 'Tâches de ' . $u['username'];
        }
    }
} else {
    // Un utilisateur simple ne voit que ses tâches
    $utilisateurs = [];
    $userId = $_SESSION['user_id'];
    $titre = 'Mes tâches';
}

$taches = getTaches($pdo, $userId, $statut, $prio, $dateDebut, $dateFin);

// La colonne "Utilisateur" ne sert que si on affiche les tâches de plusieurs personnes
$colonneUtilisateur = false;
if ($voirTout && $userId == 0) {
    $colonneUtilisateur = true;
}

require 'header.php';
?>

<!-- Filtres -->
<form method="get" class="filtres">
    <?php if ($voirTout) { ?>
        <label for="user_id">Utilisateur</label>
        <select id="user_id" name="user_id">
            <option value="">Tous</option>
            <?php foreach ($utilisateurs as $u) { ?>
                <option value="<?php echo $u['id']; ?>" <?php if ($u['id'] == $userId) { echo 'selected'; } ?>><?php echo htmlspecialchars($u['username']); ?></option>
            <?php } ?>
        </select>
    <?php } ?>

    <label for="status">Statut</label>
    <select id="status" name="status">
        <option value="">Tous</option>
        <?php foreach ($listeStatuts as $cle => $texte) { ?>
            <option value="<?php echo $cle; ?>" <?php if ($cle == $statut) { echo 'selected'; } ?>><?php echo $texte; ?></option>
        <?php } ?>
    </select>

    <label for="priority">Priorité</label>
    <select id="priority" name="priority">
        <option value="0">Toutes</option>
        <?php foreach ($listePriorites as $cle => $texte) { ?>
            <option value="<?php echo $cle; ?>" <?php if ($cle == $prio) { echo 'selected'; } ?>><?php echo $texte; ?></option>
        <?php } ?>
    </select>

    <label for="date_debut">Date de début</label>
    <input type="date" id="date_debut" name="date_debut" value="<?php echo $dateDebut; ?>">

    <label for="date_fin">Date de fin</label>
    <input type="date" id="date_fin" name="date_fin" value="<?php echo $dateFin; ?>">

    <button type="submit" class="btn">Filtrer</button>
    <a href="allTasks.php">Réinitialiser</a>
</form>

<?php if ($erreurDates != '') { ?>
    <p class="flash erreur"><?php echo $erreurDates; ?></p>
<?php } ?>

<p class="barre">
    <span><?php echo count($taches); ?> tâche(s)</span>
    <a class="btn" href="addTask.php">Nouvelle tâche</a>
</p>

<?php if (count($taches) == 0) { ?>

    <p class="vide">Aucune tâche.</p>

<?php } else { ?>

<table>
    <thead>
        <tr>
            <th>Titre</th>
            <?php if ($colonneUtilisateur) { ?><th>Utilisateur</th><?php } ?>
            <th>Catégorie</th>
            <th>Statut</th>
            <th>Priorité</th>
            <th>Créée le</th>
            <th>Échéance</th>
            <th>Commentaires</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($taches as $t) { ?>
        <tr>
            <td>
                <a class="lien-tache" href="task.php?id=<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['title']); ?></a>
                <?php if ($t['description'] != '') { ?>
                    <br><small><?php echo htmlspecialchars($t['description']); ?></small>
                <?php } ?>
            </td>

            <?php if ($colonneUtilisateur) { ?>
                <td><?php echo htmlspecialchars($t['username']); ?></td>
            <?php } ?>

            <td><?php if ($t['categorie'] != null) { echo htmlspecialchars($t['categorie']); } else { echo 'Aucune'; } ?></td>
            <td><span class="badge <?php echo $t['status']; ?>"><?php echo $listeStatuts[$t['status']]; ?></span></td>
            <td><span class="prio p<?php echo $t['priority']; ?>"><?php echo $listePriorites[$t['priority']]; ?></span></td>
            <td><?php echo date('d/m/Y', strtotime($t['created_at'])); ?></td>
            <td><?php if ($t['due_date'] != null) { echo date('d/m/Y', strtotime($t['due_date'])); } else { echo '-'; } ?></td>
            <td class="nb-commentaires"><a href="task.php?id=<?php echo $t['id']; ?>#commentaires"><?php echo $t['nb_commentaires']; ?></a></td>
            <td>
                <?php // Modifier / supprimer : seulement ses propres tâches, sauf pour l'administrateur
                if (estAdmin() || $t['user_id'] == $_SESSION['user_id']) { ?>
                    <a href="editTask.php?id=<?php echo $t['id']; ?>">Modifier</a>
                    <form method="post" action="deleteTask.php" class="inline" onsubmit="return confirm('Supprimer cette tâche ?');">
                        <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
                        <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                        <button type="submit" class="lien-danger">Supprimer</button>
                    </form>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<?php } ?>

<?php require 'footer.php'; ?>
