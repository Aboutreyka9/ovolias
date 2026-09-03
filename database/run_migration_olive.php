<?php
// Script de migration Olive Service - Ajout des colonnes manquantes
// Auteur : Kilo
// Date : 2026-08-31
// Description : Ajoute toutes les colonnes métier référencées par le code
//               PHP mais absentes du schéma de base `olive`.
//
// Usage : php database/run_migration_olive.php

require_once __DIR__ . '/../config/Database.php';

try {
    $db = (new Database())->getCon();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $errors = [];

    // Helper: vérifier si une colonne existe
    $columnExists = function(string $table, string $column) use ($db): bool {
        $stmt = $db->prepare("DESCRIBE `$table` `$column`");
        $stmt->execute();
        return $stmt->fetchColumn() !== false;
    };

    // Helper: vérifier si un index existe
    $indexExists = function(string $table, string $index) use ($db): bool {
        $stmt = $db->prepare("SHOW INDEX FROM `$table` WHERE Key_name = ?");
        $stmt->execute([$index]);
        return $stmt->fetchColumn() !== false;
    };

    // Helper: exécuter une migration
    $run = function(string $sql, string $description) use ($db, &$errors): bool {
        try {
            $db->exec($sql);
            echo "[OK] $description\n";
            return true;
        } catch (Exception $e) {
            $errors[] = "[ERREUR] $description : " . $e->getMessage();
            return false;
        }
    };

    // Helper: ajouter une colonne si elle n'existe pas
    $addColumn = function(string $table, string $column, string $definition, string $description) use ($columnExists, $run): void {
        if (!$columnExists($table, $column)) {
            $run("ALTER TABLE `$table` ADD COLUMN `$column` $definition", $description);
        } else {
            echo "[SKIP] Colonne $table.$column existe déjà\n";
        }
    };

    // Helper: ajouter un index si celui-ci n'existe pas
    $addIndex = function(string $table, string $index, string $columns, string $description) use ($indexExists, $run): void {
        if (!$indexExists($table, $index)) {
            $run("ALTER TABLE `$table` ADD INDEX `$index` ($columns)", $description);
        } else {
            echo "[SKIP] Index $table.$index existe déjà\n";
        }
    };

    // Helper: créer une table si elle n'existe pas
    $createTable = function(string $tableName, string $createSql, string $description) use ($db, $errors, $run): void {
        $stmt = $db->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$tableName]);
        if ($stmt->fetchColumn() === false) {
            $run($createSql, $description);
        } else {
            echo "[SKIP] Table $tableName existe déjà\n";
        }
    };

    echo "=== Migration Olive Service - Ajout des colonnes métier ===\n\n";


    echo "\n=== Résumé ===\n";
    if (empty($errors)) {
        echo "Migration terminée avec succès.\n";
    } else {
        echo "Migration terminée avec " . count($errors) . " erreur(s) :\n";
        foreach ($errors as $error) {
            echo "  $error\n";
        }
    }

} catch (Exception $e) {
    echo "ERREUR FATALE : " . $e->getMessage() . "\n";
    exit(1);
}
