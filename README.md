# Gestion de projet

Application web de gestion de tâches réalisée en PHP / MySQL dans le cadre du BTS SIO (option SLAM).

## Fonctionnalités

- Connexion / déconnexion avec trois rôles : Administrateur, Modérateur, Utilisateur
- Gestion des utilisateurs (ajout, modification, suppression) réservée à l'administrateur
- Gestion des tâches (ajout, modification, suppression)
- Filtres sur la liste des tâches : utilisateur, statut, priorité, date de création (début / fin)
- Commentaires sur les tâches, avec réponses

## Installation (MAMP)

1. Copier le dossier dans `C:\MAMP\htdocs\`
2. Dans phpMyAdmin, importer `create_db.sql` puis `jeu_de_donnees.sql`
3. Vérifier les paramètres de connexion dans `include/bdd.php`
4. Ouvrir `http://localhost/<nom du dossier>/login.php`

Comptes de démonstration (mot de passe : `password123`) :

- Administrateur : camille.martin@example.com
- Modérateur : sarah.benali@example.com
- Utilisateur : lea.dubois@example.com
