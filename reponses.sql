-- Ajout des réponses aux commentaires dans la base mon_app
-- A importer une seule fois dans phpMyAdmin (onglet Importer)
-- parent_id = id du commentaire auquel on répond (NULL pour un commentaire normal)

USE mon_app;

ALTER TABLE comments
    ADD COLUMN parent_id INT NULL DEFAULT NULL AFTER task_id,
    ADD CONSTRAINT fk_comments_parent FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE;
