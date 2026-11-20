<?php
require 'include/bdd.php';
require 'include/auth.php';
require 'include/users.php';

// Page réservée aux administrateurs
verifierAdmin();

$utilisateurs = getUsers($pdo);

$titre = 'Liste des utilisateurs';
require 'header.php';
?>

<p><a class="btn" href="addUser.php">Ajouter un utilisateur</a></p>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nom d'utilisateur</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Tâches</th>
            <th>Inscrit le</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($utilisateurs as $u) { ?>
        <tr>
            <td><?php echo $u['id']; ?></td>
            <td><?php echo htmlspecialchars($u['username']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td><?php if ($u['role'] != null) { echo htmlspecialchars($u['role']); } else { echo 'Aucun'; } ?></td>
            <td><a href="allTasks.php?user_id=<?php echo $u['id']; ?>"><?php echo $u['nb_taches']; ?></a></td>
            <td><?php echo $u['created_at']; ?></td>
            <td>
                <a href="editUser.php?id=<?php echo $u['id']; ?>">Modifier</a>

                <?php // On ne peut pas supprimer son propre compte
                if ($u['id'] != $_SESSION['user_id']) { ?>
                    <form method="post" action="deleteUser.php" class="inline" onsubmit="return confirm('Supprimer cet utilisateur et toutes ses tâches ?');">
                        <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                        <button type="submit" class="lien-danger">Supprimer</button>
                    </form>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<?php require 'footer.php'; ?>
