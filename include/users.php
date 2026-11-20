<?php
// Fonctions qui travaillent sur la table users


// Récupère tous les utilisateurs, avec le nom de leur rôle et leur nombre de tâches
function getUsers($pdo) {
    $sql = "SELECT users.id, users.username, users.email, users.created_at, roles.name AS role,
                   (SELECT COUNT(*) FROM tasks WHERE tasks.user_id = users.id) AS nb_taches
            FROM users
            LEFT JOIN roles ON users.role_id = roles.id
            ORDER BY users.id";
    $requete = $pdo->prepare($sql);
    $requete->execute();
    return $requete->fetchAll();
}

// Récupère un utilisateur grâce à son id
function getUser($pdo, $id) {
    $sql = "SELECT users.*, roles.name AS role
            FROM users
            LEFT JOIN roles ON users.role_id = roles.id
            WHERE users.id = :id";
    $requete = $pdo->prepare($sql);
    $requete->execute(['id' => $id]);
    return $requete->fetch();
}

// Récupère un utilisateur grâce à son email (utilisé pour la connexion)
function getUserParEmail($pdo, $email) {
    $sql = "SELECT users.*, roles.name AS role
            FROM users
            LEFT JOIN roles ON users.role_id = roles.id
            WHERE users.email = :email";
    $requete = $pdo->prepare($sql);
    $requete->execute(['email' => $email]);
    return $requete->fetch();
}

// Récupère la liste des rôles
function getRoles($pdo) {
    $requete = $pdo->prepare("SELECT * FROM roles ORDER BY id");
    $requete->execute();
    return $requete->fetchAll();
}

// Vérifie si le nom d'utilisateur ou l'email est déjà pris par un autre utilisateur
// ($idIgnore = l'utilisateur qu'on modifie, 0 pour un ajout)
function existeDeja($pdo, $username, $email, $idIgnore) {
    $sql = "SELECT COUNT(*) FROM users
            WHERE (username = :username OR email = :email) AND id != :id";
    $requete = $pdo->prepare($sql);
    $requete->execute(['username' => $username, 'email' => $email, 'id' => $idIgnore]);

    if ($requete->fetchColumn() > 0) {
        return true;
    }
    return false;
}

// Ajoute un utilisateur (le mot de passe est haché avant d'être enregistré)
function ajouterUser($pdo, $username, $email, $motDePasse, $roleId) {
    $sql = "INSERT INTO users (username, email, password, role_id)
            VALUES (:username, :email, :password, :role_id)";
    $requete = $pdo->prepare($sql);
    $requete->execute([
        'username' => $username,
        'email'    => $email,
        'password' => password_hash($motDePasse, PASSWORD_DEFAULT),
        'role_id'  => $roleId
    ]);
}

// Modifie le nom, l'email et le rôle d'un utilisateur
function modifierUser($pdo, $id, $username, $email, $roleId) {
    $sql = "UPDATE users SET username = :username, email = :email, role_id = :role_id
            WHERE id = :id";
    $requete = $pdo->prepare($sql);
    $requete->execute([
        'username' => $username,
        'email'    => $email,
        'role_id'  => $roleId,
        'id'       => $id
    ]);
}

// Modifie le mot de passe d'un utilisateur
function modifierMotDePasse($pdo, $id, $motDePasse) {
    $requete = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
    $requete->execute([
        'password' => password_hash($motDePasse, PASSWORD_DEFAULT),
        'id'       => $id
    ]);
}

// Supprime un utilisateur (ses tâches sont supprimées automatiquement par la base)
// Retourne true si l'utilisateur existait
function supprimerUser($pdo, $id) {
    $requete = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $requete->execute(['id' => $id]);

    if ($requete->rowCount() > 0) {
        return true;
    }
    return false;
}
