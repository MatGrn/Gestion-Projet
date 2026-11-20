<?php
require 'include/bdd.php';
require 'include/auth.php';
require 'include/users.php';

// Si on est déjà connecté, pas besoin de revenir sur cette page
if (isset($_SESSION['user_id'])) {
    if (estAdmin()) {
        header('Location: index.php');
    } else {
        header('Location: allTasks.php');
    }
    exit;
}

$email = '';
$erreur = '';

// Le formulaire a été envoyé
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verifierJeton();

    $email = trim($_POST['email']);
    $motDePasse = $_POST['password'];

    // On cherche l'utilisateur avec cet email
    $utilisateur = getUserParEmail($pdo, $email);

    // On vérifie le mot de passe tapé avec celui enregistré (haché) dans la base
    if ($utilisateur && password_verify($motDePasse, $utilisateur['password'])) {
        // Nouvel identifiant de session après la connexion (sécurité)
        session_regenerate_id(true);

        $_SESSION['user_id'] = $utilisateur['id'];
        $_SESSION['username'] = $utilisateur['username'];
        $_SESSION['role'] = $utilisateur['role'];

        $_SESSION['message'] = 'Bienvenue ' . $utilisateur['username'] . ' !';
        $_SESSION['type_message'] = 'ok';

        // L'administrateur arrive sur la liste des utilisateurs, les autres sur leurs tâches
        if (estAdmin()) {
            header('Location: index.php');
        } else {
            header('Location: allTasks.php');
        }
        exit;
    }

    // Même message si l'email n'existe pas ou si le mot de passe est faux
    $erreur = 'Email ou mot de passe incorrect.';
}

$titre = 'Connexion';
require 'header.php';
?>

<?php if ($erreur != '') { ?>
    <p class="flash erreur"><?php echo $erreur; ?></p>
<?php } ?>

<form method="post" class="formulaire">
    <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

    <label for="password">Mot de passe</label>
    <input type="password" id="password" name="password" required>

    <p><button type="submit" class="btn">Se connecter</button></p>
</form>

<?php require 'footer.php'; ?>
