-- Jeu de données de démonstration pour la base mon_app
-- A importer dans phpMyAdmin (onglet Importer) : remplace toutes les données existantes.
-- La table comments doit avoir la colonne parent_id (importer reponses.sql une fois avant si besoin).
-- Mot de passe de tous les comptes : password123

USE mon_app;

DELETE FROM comments WHERE id > 0;
DELETE FROM tasks WHERE id > 0;
DELETE FROM users WHERE id > 0;
DELETE FROM categories WHERE id > 0;
DELETE FROM roles WHERE id > 0;

ALTER TABLE comments AUTO_INCREMENT = 1;
ALTER TABLE tasks AUTO_INCREMENT = 1;
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE categories AUTO_INCREMENT = 1;
ALTER TABLE roles AUTO_INCREMENT = 1;


-- Rôles (le nom "Administrateur" est utilisé par l'application, ne pas le modifier)
INSERT INTO roles (id, name) VALUES
    (1, 'Administrateur'),
    (2, 'Utilisateur'),
    (3, 'Modérateur');


-- Catégories
INSERT INTO categories (id, name) VALUES
    (1, 'Développement'),
    (2, 'Base de données'),
    (3, 'Design'),
    (4, 'Tests'),
    (5, 'Documentation'),
    (6, 'Réunion'),
    (7, 'Support'),
    (8, 'Déploiement');


-- Utilisateurs
INSERT INTO users (id, username, email, password, role_id, created_at) VALUES
    (1,  'camille.martin',  'camille.martin@example.com',  '$2y$12$5UuHcfhsaSF7nbsarECn..kZaCEppayju7fwPU4Hhz4RbvaN5qxWe', 1, '2026-09-01 09:12:00'),
    (2,  'thomas.lefevre',  'thomas.lefevre@example.com',  '$2y$12$5UuHcfhsaSF7nbsarECn..kZaCEppayju7fwPU4Hhz4RbvaN5qxWe', 1, '2026-09-01 09:20:00'),
    (3,  'sarah.benali',    'sarah.benali@example.com',    '$2y$12$5UuHcfhsaSF7nbsarECn..kZaCEppayju7fwPU4Hhz4RbvaN5qxWe', 3, '2026-09-02 14:05:00'),
    (4,  'julien.moreau',   'julien.moreau@example.com',   '$2y$12$5UuHcfhsaSF7nbsarECn..kZaCEppayju7fwPU4Hhz4RbvaN5qxWe', 3, '2026-09-02 14:30:00'),
    (5,  'lea.dubois',      'lea.dubois@example.com',      '$2y$12$5UuHcfhsaSF7nbsarECn..kZaCEppayju7fwPU4Hhz4RbvaN5qxWe', 2, '2026-09-08 10:00:00'),
    (6,  'hugo.bernard',    'hugo.bernard@example.com',    '$2y$12$5UuHcfhsaSF7nbsarECn..kZaCEppayju7fwPU4Hhz4RbvaN5qxWe', 2, '2026-09-08 10:15:00'),
    (7,  'ines.petit',      'ines.petit@example.com',      '$2y$12$5UuHcfhsaSF7nbsarECn..kZaCEppayju7fwPU4Hhz4RbvaN5qxWe', 2, '2026-09-15 11:40:00'),
    (8,  'maxime.roux',     'maxime.roux@example.com',     '$2y$12$5UuHcfhsaSF7nbsarECn..kZaCEppayju7fwPU4Hhz4RbvaN5qxWe', 2, '2026-09-22 09:05:00'),
    (9,  'chloe.garnier',   'chloe.garnier@example.com',   '$2y$12$5UuHcfhsaSF7nbsarECn..kZaCEppayju7fwPU4Hhz4RbvaN5qxWe', 2, '2026-09-29 16:20:00'),
    (10, 'nathan.faure',    'nathan.faure@example.com',    '$2y$12$5UuHcfhsaSF7nbsarECn..kZaCEppayju7fwPU4Hhz4RbvaN5qxWe', 2, '2026-10-05 08:45:00');


-- Tâches
-- status : todo = à faire, doing = en cours, done = terminée / priority : 1 = basse, 2 = normale, 3 = haute
INSERT INTO tasks (user_id, category_id, title, description, status, priority, due_date, created_at) VALUES

-- Camille Martin (cheffe de projet)
    (1, 5, 'Rédiger le cahier des charges du portail client', 'Recueillir les besoins avec le client et valider le périmètre fonctionnel', 'done', 3, '2026-09-18', '2026-09-01 10:30:00'),
    (1, 6, 'Planifier les sprints de novembre', 'Découper les fonctionnalités restantes et répartir la charge dans l''équipe', 'done', 2, '2026-11-03', '2026-10-27 09:00:00'),
    (1, 6, 'Préparer la démo client du 20 novembre', 'Scénario de démonstration et données de présentation', 'doing', 3, '2026-11-19', '2026-11-05 14:10:00'),
    (1, 6, 'Point hebdomadaire avec l''équipe', NULL, 'todo', 1, '2026-11-16', '2026-11-09 17:30:00'),
    (1, 5, 'Valider le budget du lot 2', 'Chiffrage du paiement en ligne à présenter à la direction', 'todo', 2, '2026-11-27', '2026-11-06 11:00:00'),

-- Thomas Lefèvre (responsable technique)
    (2, 1, 'Choisir l''architecture du back-end', 'Comparer Symfony et une structure PHP maison, rédiger la décision', 'done', 3, '2026-09-25', '2026-09-03 09:15:00'),
    (2, 8, 'Mettre en place le serveur de recette', 'Installation Apache, PHP 8.3 et MariaDB sur la VM de test', 'done', 2, '2026-10-09', '2026-09-28 10:00:00'),
    (2, 1, 'Relire les merge requests en attente', 'Revue de code des branches authentification et tableau de bord', 'doing', 2, '2026-11-13', '2026-11-10 09:30:00'),
    (2, 1, 'Corriger la session encore active après déconnexion', 'Le cookie de session reste valide après la déconnexion sur Firefox', 'doing', 3, '2026-11-12', '2026-11-10 15:45:00'),
    (2, 8, 'Automatiser le déploiement en recette', 'Script de déploiement depuis la branche develop', 'todo', 2, '2026-11-30', '2026-11-04 16:00:00'),
    (2, 8, 'Mettre en production la version 1.0', 'Sauvegarde de la base, déploiement puis vérifications', 'todo', 3, '2026-12-15', '2026-11-04 16:05:00'),

-- Sarah Benali (support)
    (3, 7, 'Traiter les tickets support de la semaine', '12 tickets ouverts, dont 3 bloquants', 'doing', 3, '2026-11-13', '2026-11-09 08:50:00'),
    (3, 7, 'Modérer les commentaires du forum client', 'Supprimer les doublons et répondre aux questions en attente', 'done', 1, '2026-11-06', '2026-11-02 09:10:00'),
    (3, 5, 'Rédiger la FAQ utilisateur', 'Questions fréquentes sur la connexion et le mot de passe oublié', 'todo', 1, '2026-11-28', '2026-11-03 14:00:00'),
    (3, 6, 'Former l''équipe support au nouvel outil', 'Session d''une heure avec démonstration', 'todo', 2, '2026-11-24', '2026-11-06 10:20:00'),

-- Julien Moreau (tests et suivi client)
    (4, 5, 'Compte rendu du comité de pilotage', 'Diffuser les décisions à l''équipe et au client', 'done', 1, '2026-10-24', '2026-10-22 18:00:00'),
    (4, 4, 'Écrire le plan de tests de la version 1.0', 'Lister les cas de tests par fonctionnalité', 'done', 2, '2026-10-30', '2026-10-13 09:40:00'),
    (4, 5, 'Mettre à jour le backlog', 'Prioriser les retours de la dernière démo', 'todo', 2, '2026-11-14', '2026-11-07 11:15:00'),
    (4, 6, 'Organiser la recette avec le client', 'Planning des sessions de tests et liste des testeurs', 'doing', 2, '2026-11-18', '2026-11-04 10:00:00'),

-- Léa Dubois (développeuse front)
    (5, 1, 'Intégrer la page de connexion', 'Formulaire, messages d''erreur et version mobile', 'done', 2, '2026-10-16', '2026-10-06 09:00:00'),
    (5, 1, 'Rendre le site responsive', 'Adapter les tableaux et le menu pour les écrans de moins de 800 px', 'doing', 2, '2026-11-10', '2026-10-28 14:30:00'),
    (5, 1, 'Développer le tableau de bord client', 'Affichage des commandes en cours et des factures', 'doing', 3, '2026-11-20', '2026-11-02 09:20:00'),
    (5, 1, 'Corriger l''affichage des dates sous Safari', 'Les champs date s''affichent au format américain', 'todo', 1, '2026-11-21', '2026-11-11 16:40:00'),

-- Hugo Bernard (développeur back)
    (6, 2, 'Créer le modèle de données', 'MCD, MLD et script de création des tables', 'done', 3, '2026-09-30', '2026-09-09 10:00:00'),
    (6, 1, 'Hacher les mots de passe existants', 'Migration des anciens mots de passe stockés en MD5', 'done', 3, '2026-10-21', '2026-10-14 11:30:00'),
    (6, 2, 'Mettre en place les sauvegardes automatiques', 'Export quotidien de la base, conservé 7 jours', 'todo', 3, '2026-11-09', '2026-10-30 15:00:00'),
    (6, 1, 'Développer l''API des commandes', 'Routes de création, consultation et annulation d''une commande', 'doing', 3, '2026-11-17', '2026-11-03 09:45:00'),
    (6, 2, 'Optimiser la requête de recherche', 'La recherche de clients prend plus de 3 secondes sur la recette', 'todo', 2, '2026-11-25', '2026-11-10 10:10:00'),

-- Inès Petit (designer)
    (7, 3, 'Maquettes de la page d''accueil', 'Trois propositions sur Figma pour validation client', 'done', 2, '2026-10-02', '2026-09-16 09:30:00'),
    (7, 3, 'Charte graphique du portail', 'Couleurs, typographies et composants de base', 'done', 2, '2026-10-10', '2026-09-30 14:00:00'),
    (7, 3, 'Maquette du tableau de bord', 'Version ordinateur et mobile', 'doing', 2, '2026-11-14', '2026-11-02 10:30:00'),
    (7, 3, 'Créer les icônes des catégories', 'Jeu de 12 icônes au format SVG', 'todo', 1, '2026-12-04', '2026-11-09 15:20:00'),

-- Maxime Roux (testeur)
    (8, 4, 'Tester le parcours d''inscription', 'Vérifier les contrôles du formulaire et l''email de confirmation', 'done', 2, '2026-10-28', '2026-10-21 09:00:00'),
    (8, 4, 'Signaler les anomalies de la page panier', 'Le total ne se met pas à jour après la suppression d''un article', 'doing', 2, '2026-11-13', '2026-11-09 10:40:00'),
    (8, 4, 'Tests de non-régression avant la démo', 'Rejouer tout le plan de tests sur la recette', 'todo', 3, '2026-11-18', '2026-11-05 14:15:00'),
    (8, 4, 'Tester la compatibilité navigateurs', 'Chrome, Firefox, Edge et Safari, sur ordinateur et mobile', 'todo', 1, '2026-11-26', '2026-11-10 11:00:00'),

-- Chloé Garnier (développeuse)
    (9, 1, 'Ajouter la pagination sur la liste des clients', '25 clients par page', 'done', 1, '2026-11-05', '2026-10-29 13:30:00'),
    (9, 1, 'Corriger l''erreur 500 sur l''export PDF', 'L''export plante quand une facture contient plus de 50 lignes', 'todo', 3, '2026-11-16', '2026-11-11 09:25:00'),
    (9, 1, 'Développer les emails de notification', 'Confirmation de commande et rappel de rendez-vous', 'doing', 2, '2026-11-19', '2026-11-04 10:50:00'),
    (9, 2, 'Créer les vues SQL pour les statistiques', NULL, 'todo', 2, '2026-12-02', '2026-11-10 16:00:00'),

-- Nathan Faure (alternant)
    (10, 5, 'Prendre en main le projet', 'Installer l''environnement local et lire la documentation technique', 'done', 1, '2026-10-12', '2026-10-05 09:00:00'),
    (10, 5, 'Rédiger la documentation de l''API', 'Décrire chaque route avec un exemple de requête et de réponse', 'doing', 1, '2026-11-27', '2026-11-03 14:00:00'),
    (10, 4, 'Écrire les tests unitaires du module utilisateurs', 'Création, modification et suppression', 'todo', 2, '2026-12-01', '2026-11-06 09:30:00'),
    (10, 6, 'Préparer le bilan de période en entreprise', NULL, 'todo', 2, '2026-12-10', '2026-11-11 17:00:00');


-- Commentaires
-- task_id = numéro de la tâche dans l'ordre d'insertion ci-dessus
-- parent_id = commentaire auquel on répond (NULL pour un commentaire normal)
INSERT INTO comments (id, task_id, parent_id, user_id, content, created_at, updated_at) VALUES
    (1,  6,  NULL, 1,  'Décision validée en réunion : structure PHP maison avec PDO.', '2026-09-25 15:00:00', NULL),
    (2,  24, NULL, 2,  'MLD relu, ok pour moi. Pense à ajouter les index sur les clés étrangères.', '2026-09-29 10:20:00', NULL),
    (3,  3,  NULL, 2,  'La recette sera à jour lundi soir, tu pourras préparer la démo dessus.', '2026-11-10 16:20:00', NULL),
    (4,  3,  3,    1,  'Parfait. Léa, le tableau de bord sera présentable pour jeudi ?', '2026-11-12 09:05:00', NULL),
    (5,  3,  3,    5,  'Oui, il manque juste l''affichage des factures, je finis ça mercredi.', '2026-11-12 10:40:00', NULL),
    (6,  9,  NULL, 2,  'Reproduit sur Firefox, pas sur Chrome. Le cookie n''est pas supprimé côté navigateur.', '2026-11-10 16:30:00', NULL),
    (7,  9,  6,    8,  'Je confirme, même chose sur Firefox mobile.', '2026-11-11 11:15:00', NULL),
    (8,  9,  6,    2,  'Corrigé sur la branche fix-session : le cookie est maintenant supprimé avec les mêmes paramètres qu''à sa création. Maxime, tu peux retester ?', '2026-11-12 17:50:00', NULL),
    (9,  8,  NULL, 2,  'Branche authentification validée, il reste le tableau de bord.', '2026-11-11 18:10:00', NULL),
    (10, 12, NULL, 3,  'Les 3 tickets bloquants concernent tous la réinitialisation du mot de passe.', '2026-11-10 09:30:00', NULL),
    (11, 12, 10,   6,  'Le lien dans l''email expirait au bout de 10 minutes au lieu de 24 h. C''est corrigé en recette.', '2026-11-10 15:10:00', NULL),
    (12, 19, NULL, 4,  'Sessions prévues le 25 et le 26 novembre, avec 4 testeurs côté client.', '2026-11-12 16:00:00', NULL),
    (13, 19, 12,   1,  'Ok pour moi, je bloque les créneaux dans les agendas.', '2026-11-12 16:45:00', NULL),
    (14, 21, NULL, 5,  'Le menu est fait, il reste les tableaux de la page commandes.', '2026-11-09 17:00:00', NULL),
    (15, 21, 14,   1,  'Pas bloquant pour la démo, on peut décaler à la semaine prochaine.', '2026-11-11 09:20:00', NULL),
    (16, 22, NULL, 7,  'La maquette mobile est sur Figma, page "Tableau de bord v2".', '2026-11-06 14:00:00', NULL),
    (17, 22, 16,   5,  'Merci, je pars sur cette version.', '2026-11-06 14:25:00', NULL),
    (18, 26, NULL, 2,  'A faire en priorité avant la mise en production : on n''a encore aucune sauvegarde de la recette.', '2026-11-10 10:00:00', NULL),
    (19, 26, 18,   6,  'Je m''y mets dès que l''API des commandes est livrée.', '2026-11-10 10:35:00', NULL),
    (20, 27, NULL, 9,  'Il me faudrait la route de création pour brancher l''email de confirmation.', '2026-11-09 11:20:00', NULL),
    (21, 27, 20,   6,  'Elle est en recette depuis ce matin, la doc est dans le README.', '2026-11-12 09:50:00', NULL),
    (22, 31, NULL, 1,  'Le client souhaite voir le montant total des factures en haut de la page.', '2026-11-05 16:00:00', NULL),
    (23, 34, NULL, 8,  'Pour reproduire : ajouter 3 articles, en supprimer un. Le total ne change pas tant qu''on ne recharge pas la page.', '2026-11-09 11:00:00', NULL),
    (24, 34, 23,   5,  'Je regarde, le calcul du total n''est sûrement pas relancé après la suppression.', '2026-11-10 09:15:00', NULL),
    (25, 35, NULL, 4,  'Le plan de tests à jour est dans le dossier partagé (version 1.2).', '2026-11-06 10:00:00', NULL),
    (26, 38, NULL, 3,  'Deux clients l''ont signalé ce matin au support.', '2026-11-11 09:10:00', NULL),
    (27, 38, 26,   9,  'La limite mémoire de PHP est atteinte, je vais générer le PDF page par page.', '2026-11-12 14:30:00', '2026-11-12 14:45:00'),
    (28, 42, NULL, 2,  'Pense à préciser les codes d''erreur renvoyés par chaque route.', '2026-11-06 11:00:00', NULL),
    (29, 42, 28,   10, 'D''accord, je les ajoute dans un tableau à la fin du document.', '2026-11-06 11:30:00', NULL),
    (30, 43, NULL, 2,  'Commence par la création, c''est la partie la plus utilisée.', '2026-11-07 09:40:00', NULL),
    (31, 9,  6,    8,  'Retesté sur Firefox ordinateur et mobile : c''est bon pour moi.', '2026-11-13 10:05:00', NULL),
    (32, 3,  NULL, 4,  'J''ai ajouté au scénario le parcours de commande complet, il est dans le dossier partagé.', '2026-11-13 15:30:00', NULL);
