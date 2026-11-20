<?php
// Formulaire utilisé par addUser.php et editUser.php
// Variables à définir avant : $username, $email, $roleId, $roles, $erreurs, $modification (true ou false)

foreach ($erreurs as $erreur) {
    echo '<p class="flash erreur">' . $erreur . '</p>';
}
?>

<form method="post" class="formulaire">
    <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">

    <label for="username">Nom d'utilisateur</label>
    <input type="text" id="username" name="username" maxlength="50" value="<?php echo htmlspecialchars($username); ?>" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" maxlength="100" value="<?php echo htmlspecialchars($email); ?>" required>

    <?php if ($modification) { ?>
        <label for="password">Mot de passe (laisser vide pour ne pas le changer)</label>
        <input type="password" id="password" name="password">
    <?php } else { ?>
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>
    <?php } ?>

    <label for="role_id">Rôle</label>
    <select id="role_id" name="role_id">
        <?php foreach ($roles as $role) { ?>
            <option value="<?php echo $role['id']; ?>" <?php if ($role['id'] == $roleId) { echo 'selected'; } ?>>
                <?php echo htmlspecialchars($role['name']); ?>
            </option>
        <?php } ?>
    </select>

    <p>
        <?php if ($modification) { ?>
            <button type="submit" class="btn">Enregistrer</button>
        <?php } else { ?>
            <button type="submit" class="btn">Ajouter</button>
        <?php } ?>
        <a href="index.php">Annuler</a>
    </p>
</form>
