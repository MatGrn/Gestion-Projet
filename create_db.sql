CREATE DATABASE IF NOT EXISTS mon_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mon_app;

-- Table des rôles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Table des catégories de tâches
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Table des tâches
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    status ENUM('todo', 'doing', 'done') NOT NULL DEFAULT 'todo',
    priority TINYINT UNSIGNED NOT NULL DEFAULT 2,
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Table des commentaires sur les tâches (parent_id = commentaire auquel on répond)
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    parent_id INT NULL DEFAULT NULL,
    user_id INT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================
-- DONNÉES DE DÉMONSTRATION
-- ============================================

-- Insertion des rôles
INSERT INTO roles (name) VALUES 
    ('Administrateur'),
    ('Utilisateur'),
    ('Modérateur');

-- Insertion des catégories
INSERT INTO categories (name) VALUES 
    ('Travail'),
    ('Personnel'),
    ('Urgent'),
    ('Projet'),
    ('Formation');

-- Insertion des utilisateurs
-- Mots de passe hashés avec password_hash()
-- Pour générer un hash : echo password_hash('monpassword', PASSWORD_DEFAULT);
INSERT INTO users (username, email, password, role_id) VALUES 
    ('logan_admin', 'logan@example.com', '$2y$12$5UuHcfhsaSF7nbsarECn..kZaCEppayju7fwPU4Hhz4RbvaN5qxWe', 1),
    ('malicia_user', 'malicia@example.com', '$2y$12$5UuHcfhsaSF7nbsarECn..kZaCEppayju7fwPU4Hhz4RbvaN5qxWe', 2),
    ('xavier_user', 'xavier@example.com', '$2y$12$5UuHcfhsaSF7nbsarECn..kZaCEppayju7fwPU4Hhz4RbvaN5qxWe', 3);

-- Note : Les 3 utilisateurs ont le mot de passe "password123"
-- (hash régénéré : celui du fichier d'origine ne correspondait pas à ce mot de passe)
-- En production, chaque utilisateur aurait son propre mot de passe

-- Insertion des tâches pour Logan (user_id = 1)
INSERT INTO tasks (user_id, category_id, title, description, status, priority, due_date) VALUES 
    (1, 1, 'Finaliser le rapport Q4', 'Préparer la présentation pour la direction', 'doing', 3, '2026-10-25'),
    (1, 4, 'Refonte du site web', 'Migration vers le nouveau design', 'todo', 2, '2026-11-15'),
    (1, 3, 'Corriger bug critique', 'Erreur 500 sur la page de paiement', 'doing', 3, '2026-10-24'),
    (1, 5, 'Formation PDO', 'Apprendre les requêtes préparées', 'done', 1, '2026-10-20');

-- Insertion des tâches pour Malicia (user_id = 2)
INSERT INTO tasks (user_id, category_id, title, description, status, priority, due_date) VALUES 
    (2, 2, 'Faire les courses', 'Légumes, fruits, pain', 'todo', 2, '2026-10-23'),
    (2, 2, 'Appeler le médecin', 'Rendez-vous de suivi annuel', 'todo', 2, '2026-10-26'),
    (2, 1, 'Réunion équipe', 'Préparer l\'ordre du jour', 'doing', 1, '2026-10-24'),
    (2, 4, 'Projet personnel', 'Application de gestion de budget', 'todo', 2, '2026-12-01');

-- Insertion des tâches pour Xavier (user_id = 3)
INSERT INTO tasks (user_id, category_id, title, description, status, priority, due_date) VALUES 
    (3, 3, 'Modérer les commentaires', 'Vérifier les signalements', 'doing', 2, '2026-10-23'),
    (3, 1, 'Mettre à jour la documentation', 'Ajouter les nouvelles fonctionnalités', 'todo', 1, '2026-10-30'),
    (3, 5, 'Webinaire sécurité', 'Préparer la présentation sur les injections SQL', 'done', 2, '2026-10-18');
