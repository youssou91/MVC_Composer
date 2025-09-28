-- Migration pour ajouter la colonne est_actif à la table utilisateur
-- Vérifie d'abord si la colonne existe déjà
SET @dbname = DATABASE();
SET @tablename = 'utilisateur';
SET @columnname = 'est_actif';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            (TABLE_SCHEMA = @dbname)
            AND (TABLE_NAME = @tablename)
            AND (COLUMN_NAME = @columnname)
    ) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TINYINT(1) NOT NULL DEFAULT 1 COMMENT "1 = actif, 0 = inactif";')
));

PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Mettre à jour tous les utilisateurs existants pour les marquer comme actifs par défaut
UPDATE utilisateur SET est_actif = 1 WHERE est_actif IS NULL OR est_actif = 0;
