<?php
// Fonctions qui travaillent sur la table tasks


// Valeurs possibles pour le statut et la priorité, avec le texte affiché
$listeStatuts = [
    'todo'  => 'À faire',
    'doing' => 'En cours',
    'done'  => 'Terminée'
];

$listePriorites = [
    1 => 'Basse',
    2 => 'Normale',
    3 => 'Haute'
];


// Vérifie qu'un texte est une vraie date au format AAAA-MM-JJ (ex : 2026-11-20)
function dateValide($date) {
    if (!is_string($date)) {
        return false;
    }
    $morceaux = explode('-', $date);
    if (count($morceaux) != 3 || strlen($date) != 10) {
        return false;
    }
    return checkdate((int) $morceaux[1], (int) $morceaux[2], (int) $morceaux[0]);
}

// Récupère les tâches avec les filtres choisis
// $userId = 0, $statut = '', $prio = 0, $dateDebut = '' ou $dateFin = '' veulent dire "pas de filtre"
// Les dates (format AAAA-MM-JJ) portent sur la date de création de la tâche
function getTaches($pdo, $userId, $statut, $prio, $dateDebut, $dateFin) {
    $sql = "SELECT tasks.*, users.username, categories.name AS categorie,
                   (SELECT COUNT(*) FROM comments WHERE comments.task_id = tasks.id) AS nb_commentaires
            FROM tasks
            INNER JOIN users ON tasks.user_id = users.id
            LEFT JOIN categories ON tasks.category_id = categories.id
            WHERE 1 = 1";
    $parametres = [];

    // On ajoute une condition pour chaque filtre rempli
    if ($userId != 0) {
        $sql = $sql . " AND tasks.user_id = :user_id";
        $parametres['user_id'] = $userId;
    }
    if ($statut != '') {
        $sql = $sql . " AND tasks.status = :status";
        $parametres['status'] = $statut;
    }
    if ($prio != 0) {
        $sql = $sql . " AND tasks.priority = :priorite";
        $parametres['priorite'] = $prio;
    }

    // Tâches créées à partir de la date de début (incluse)
    if ($dateDebut != '') {
        $sql = $sql . " AND DATE(tasks.created_at) >= :date_debut";
        $parametres['date_debut'] = $dateDebut;
    }
    // Tâches créées jusqu'à la date de fin (incluse)
    if ($dateFin != '') {
        $sql = $sql . " AND DATE(tasks.created_at) <= :date_fin";
        $parametres['date_fin'] = $dateFin;
    }

    // Les plus importantes en premier, puis par date d'échéance
    $sql = $sql . " ORDER BY tasks.priority DESC, tasks.due_date ASC";

    $requete = $pdo->prepare($sql);
    $requete->execute($parametres);
    return $requete->fetchAll();
}

// Récupère une tâche grâce à son id (avec le nom de l'utilisateur et de la catégorie)
function getTache($pdo, $id) {
    $sql = "SELECT tasks.*, users.username, categories.name AS categorie
            FROM tasks
            INNER JOIN users ON tasks.user_id = users.id
            LEFT JOIN categories ON tasks.category_id = categories.id
            WHERE tasks.id = :id";
    $requete = $pdo->prepare($sql);
    $requete->execute(['id' => $id]);
    return $requete->fetch();
}

// Récupère la liste des catégories
function getCategories($pdo) {
    $requete = $pdo->prepare("SELECT * FROM categories ORDER BY name");
    $requete->execute();
    return $requete->fetchAll();
}

// Ajoute une tâche
function ajouterTache($pdo, $userId, $categorieId, $titre, $description, $statut, $priorite, $echeance) {
    $sql = "INSERT INTO tasks (user_id, category_id, title, description, status, priority, due_date)
            VALUES (:user_id, :category_id, :title, :description, :status, :priority, :due_date)";
    $requete = $pdo->prepare($sql);
    $requete->execute([
        'user_id'     => $userId,
        'category_id' => $categorieId,
        'title'       => $titre,
        'description' => $description,
        'status'      => $statut,
        'priority'    => $priorite,
        'due_date'    => $echeance
    ]);
}

// Modifie une tâche
function modifierTache($pdo, $id, $userId, $categorieId, $titre, $description, $statut, $priorite, $echeance) {
    $sql = "UPDATE tasks SET user_id = :user_id, category_id = :category_id, title = :title,
                   description = :description, status = :status, priority = :priority, due_date = :due_date
            WHERE id = :id";
    $requete = $pdo->prepare($sql);
    $requete->execute([
        'user_id'     => $userId,
        'category_id' => $categorieId,
        'title'       => $titre,
        'description' => $description,
        'status'      => $statut,
        'priority'    => $priorite,
        'due_date'    => $echeance,
        'id'          => $id
    ]);
}

// Supprime une tâche (ses commentaires sont supprimés automatiquement par la base)
function supprimerTache($pdo, $id) {
    $requete = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $requete->execute(['id' => $id]);
}
