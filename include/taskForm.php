<?php
// Formulaire utilisé par addTask.php et editTask.php
// Variables à définir avant : $titreTache, $description, $userIdTache, $categorieId, $statut, $priorite,
// $echeance, $utilisateurs, $categories, $erreurs, $modification (true ou false)

foreach ($erreurs as $erreur) {
    echo '<p class="flash erreur">' . $erreur . '</p>';
}
?>

<form method="post" class="formulaire">
    <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">

    <label for="title">Titre</label>
    <input type="text" id="title" name="title" maxlength="150" value="<?php echo htmlspecialchars($titreTache); ?>" required>

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>

    <?php // Seul l'administrateur choisit à qui la tâche est attribuée
    if (estAdmin()) { ?>
        <label for="user_id">Attribuée à</label>
        <select id="user_id" name="user_id">
            <?php foreach ($utilisateurs as $u) { ?>
                <option value="<?php echo $u['id']; ?>" <?php if ($u['id'] == $userIdTache) { echo 'selected'; } ?>><?php echo htmlspecialchars($u['username']); ?></option>
            <?php } ?>
        </select>
    <?php } ?>

    <label for="category_id">Catégorie</label>
    <select id="category_id" name="category_id">
        <option value="">Aucune</option>
        <?php foreach ($categories as $c) { ?>
            <option value="<?php echo $c['id']; ?>" <?php if ($c['id'] == $categorieId) { echo 'selected'; } ?>><?php echo htmlspecialchars($c['name']); ?></option>
        <?php } ?>
    </select>

    <label for="status">Statut</label>
    <select id="status" name="status">
        <?php foreach ($listeStatuts as $cle => $texte) { ?>
            <option value="<?php echo $cle; ?>" <?php if ($cle == $statut) { echo 'selected'; } ?>><?php echo $texte; ?></option>
        <?php } ?>
    </select>

    <label for="priority">Priorité</label>
    <select id="priority" name="priority">
        <?php foreach ($listePriorites as $cle => $texte) { ?>
            <option value="<?php echo $cle; ?>" <?php if ($cle == $priorite) { echo 'selected'; } ?>><?php echo $texte; ?></option>
        <?php } ?>
    </select>

    <label for="due_date">Échéance</label>
    <input type="date" id="due_date" name="due_date" value="<?php echo htmlspecialchars($echeance); ?>">

    <p>
        <?php if ($modification) { ?>
            <button type="submit" class="btn">Enregistrer</button>
        <?php } else { ?>
            <button type="submit" class="btn">Ajouter</button>
        <?php } ?>
        <a href="allTasks.php">Annuler</a>
    </p>
</form>
