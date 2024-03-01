<?php

namespace App\Model;

use mysqli;
use Core\Kernel\Config;

class ConsoleModel
{
    private static function getConnection()
    {
        $config = new Config();
        $db_info = array(
            'db_name' => $config->get('db_name'),
            'db_user' => $config->get('db_user'),
            'db_pass' => $config->get('db_pass'),
            'db_host' => $config->get('db_host')
        );

        $conn = new mysqli($db_info['db_host'], $db_info['db_user'], $db_info['db_pass'], $db_info['db_name']);

        if ($conn->connect_error) {
            die("\033[31mLa connexion a échoué : " . $conn->connect_error . "\033[0m");
        }

        return $conn;
    }

    public static function createTable($tableName, $tableFields)
    {
        $connection = self::getConnection();
        $sql = self::generateCreateTableQuery($tableName, $tableFields);

        if ($connection->query($sql) === TRUE) {
            echo "\033[35mTable créée avec succès.\n\033[0m";
        } else {
            echo "\033[31mErreur lors de la création de la table : " . $connection->error . "\033[0m";
        }

        $connection->close();
    }

    private static function generateCreateTableQuery($tableName, $tableFields)
    {
        $sql = "CREATE TABLE $tableName (";
        foreach ($tableFields as $fieldName => $fieldType) {
            $sql .= "$fieldName $fieldType, ";
        }
        $sql = rtrim($sql, ", ");
        $sql .= ")";

        return $sql;
    }

    public static function tableExists($tableName)
    {
        $connection = self::getConnection();
        $sql = "SHOW TABLES LIKE '$tableName'";
        $result = $connection->query($sql);
        $exists = $result->num_rows > 0;
        $connection->close();
        return $exists;
    }

    public static function getAllTables()
    {
        $connection = self::getConnection();
        $database = self::dbinfo()['db_name'];

        $tables = array();

        $result = $connection->query("SHOW TABLES FROM $database");

        if ($result) {
            while ($row = $result->fetch_row()) {
                $tables[] = $row[0];
            }

            $result->close();
        }

        $connection->close();

        return $tables;
    }

    public static function dropTable($tableName)
    {
        $connection = self::getConnection();
        $sql = "DROP TABLE IF EXISTS $tableName";

        if ($connection->query($sql) === TRUE) {
            $connection->close();
            return true;
        } else {
            $connection->close();
            return false;
        }
    }

    public static function exportDatabase()
    {
        $db_info = self::dbinfo();
        $db_name = $db_info['db_name'];
        $db_user = $db_info['db_user'];
        $db_pass = $db_info['db_pass'];
        $db_host = $db_info['db_host'];

        $timestamp = date('d-m-Y_H-i-s');
        $backup_file = 'bdd/' . $db_name . '_' . $timestamp . '.sql';

        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

        if ($conn->connect_error) {
            die("La connexion a échoué : " . $conn->connect_error);
        }

        $tables = array();
        $result = $conn->query("SHOW TABLES");
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }

        $handle = fopen($backup_file, 'w');

        foreach ($tables as $table) {
            $result = $conn->query("SELECT * FROM $table");
            $num_fields = $result->field_count;

            fwrite($handle, "DROP TABLE IF EXISTS $table;\n");
            $row2 = $conn->query("SHOW CREATE TABLE $table")->fetch_assoc();
            fwrite($handle, $row2["Create Table"] . ";\n");

            while ($row = $result->fetch_assoc()) {
                $sql = "INSERT INTO $table VALUES(";
                for ($i = 0; $i < $num_fields; $i++) {
                    $row[$i] = $conn->real_escape_string($row[$i]);
                    $row[$i] = "'" . $row[$i] . "'";
                }
                $sql .= implode(',', $row) . ");\n";
                fwrite($handle, $sql);
            }
            fwrite($handle, "\n");
        }

        fclose($handle);
        $conn->close();

        echo "\033[32mLa base de données a été exportée avec succès vers : $backup_file\033[0m \n";
    }

    public static function exportTable($tableName)
    {
        $db_info = self::dbinfo();
        $db_name = $db_info['db_name'];
        $db_user = $db_info['db_user'];
        $db_pass = $db_info['db_pass'];
        $db_host = $db_info['db_host'];

        $timestamp = date('d-m-Y_H-i-s');
        $backup_file = 'bdd/' . $tableName . '_' . $timestamp . '.sql';

        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

        if ($conn->connect_error) {
            die("La connexion a échoué : " . $conn->connect_error);
        }

        if (!self::tableExists($tableName)) {
            die("La table '$tableName' n'existe pas dans la base de données.");
        }

        $handle = fopen($backup_file, 'w');

        $result = $conn->query("SHOW CREATE TABLE $tableName");
        $row = $result->fetch_row();
        fwrite($handle, $row[1] . ";\n");

        $result = $conn->query("SELECT * FROM $tableName");
        $num_fields = $result->field_count;

        while ($row = $result->fetch_assoc()) {
            $sql = "INSERT INTO $tableName VALUES(";
            for ($i = 0; $i < $num_fields; $i++) {
                $row[$i] = $conn->real_escape_string($row[$i]);
                $row[$i] = "'" . $row[$i] . "'";
            }
            $sql .= implode(',', $row) . ");\n";
            fwrite($handle, $sql);
        }

        fclose($handle);
        $conn->close();

        echo "\033[32mLa table '$tableName' a été exportée avec succès vers : $backup_file\033[0m \n";
    }

    public static function addColumnToTable($tableName, $columnName, $columnType)
    {
        $conn = self::getConnection();

        if (!self::tableExists($tableName)) {
            die("La table '$tableName' n'existe pas dans la base de données.");
        }

        $alterQuery = "ALTER TABLE $tableName ADD COLUMN $columnName $columnType";

        if ($conn->query($alterQuery) === TRUE) {
            echo "\033[32mLa colonne '$columnName' a été ajoutée avec succès à la table '$tableName'.\033[0m  \n";
        } else {
            echo "\033[31mErreur lors de l'ajout de la colonne : " . $conn->error . "\033[0m";
        }

        $conn->close();
    }

    public static function dropColumnFromTable($tableName, $columnName)
    {
        $conn = self::getConnection();

        $sql = "ALTER TABLE $tableName DROP COLUMN $columnName";

        if ($conn->query($sql) === TRUE) {
            echo "\033[32mLa colonne '$columnName' a été supprimée avec succès de la table '$tableName'.\n\033[0m ";
        } else {
            echo "\033[31mErreur lors de la suppression de la colonne '$columnName' de la table '$tableName' : " . $conn->error . "\n\033[0m \n";
        }

        $conn->close();
    }

    public static function dbinfo()
    {
        $config = new Config();
        $db_info = array(
            'db_name' => $config->get('db_name'),
            'db_user' => $config->get('db_user'),
            'db_pass' => $config->get('db_pass'),
            'db_host' => $config->get('db_host')
        );

        return $db_info;
    }

    public static function columnExists($tableName, $columnName)
    {
        $connection = self::getConnection();
        $database = self::dbinfo()['db_name'];

        $sql = "SHOW COLUMNS FROM $tableName LIKE '$columnName'";
        $result = $connection->query($sql);

        $exists = $result->num_rows > 0;

        $connection->close();

        return $exists;
    }

    public static function createTableFromConsole()
    {
        $tableName = '';
    
        while (true) {
            echoGreen("Nom de la table ? \n ");
            $tableName = trim(fgets(STDIN));
    
            if (!self::tableExists($tableName)) {
                $fieldTypes = array(
                    'INT',
                    'VARCHAR',
                    'TEXT',
                    'DATE',
                    'DATETIME',
                    'BOOLEAN',
                    'FLOAT',
                    'DOUBLE',
                    'DECIMAL'
                );
    
                $tableFields = array();
                echo "Création de la table : \n";
    
                $tableFields['id'] = 'INT AUTO_INCREMENT PRIMARY KEY';
    
                echoMagenta("Un champ 'id' de type 'INT AUTO_INCREMENT PRIMARY KEY' a été automatiquement ajouté.\n");
    
                self::createTable($tableName, $tableFields);
                break;
            }
    
            echoRed("La table '$tableName' existe déjà. Veuillez choisir un autre nom.\n");
        }
    
        $fieldTypes = array(
            'INT',
            'VARCHAR',
            'TEXT',
            'DATE',
            'DATETIME',
            'BOOLEAN',
            'FLOAT',
            'DOUBLE',
            'DECIMAL'
        );
    
        while (true) {
            echo "Nom du champ (ou 'quit' pour terminer) : ";
            $fieldName = trim(fgets(STDIN));
    
            if ($fieldName == 'quit') {
                break;
            }
    
            if (self::columnExists($tableName, $fieldName)) {
                echoRed("Le champ '$fieldName' existe déjà. Veuillez choisir un autre nom.\n");
                continue;
            }
    
            if (strtolower($fieldName) === 'id') {
                echoRed("Vous ne pouvez pas utiliser 'id' comme nom de champ. Veuillez choisir un autre nom.\n");
                continue;
            }
    
            echoGreen("Le champ '$fieldName' peut-il être NULL ? (oui/non) : ");
            $nullableInput = trim(fgets(STDIN));
    
            $nullable = strtolower($nullableInput) === 'oui' ? 'NULL' : 'NOT NULL';
    
            echoGreen("Type du champ (");
            foreach ($fieldTypes as $type) {
                echoGreen("$type, ");
            }
            echoGreen(") : ");
            $fieldType = trim(fgets(STDIN));
    
            if (!in_array($fieldType, $fieldTypes)) {
                echoRed("Type de champ non valide. Veuillez choisir parmi les types suivants : " . implode(', ', $fieldTypes) . "\n");
                continue;
            }
    
            $fieldSizePart = '';
            if ($fieldType === 'VARCHAR' || $fieldType === 'INT') {
                echoGreen("Taille du champ (laissez vide pour la valeur par défaut) : ");
                $fieldSize = trim(fgets(STDIN));
                $fieldSizePart = !empty($fieldSize) ? "($fieldSize)" : "";
            }
    
            $tableFields[$fieldName] = "$fieldType$fieldSizePart $nullable";
    
            // Mettre à jour la table avec les champs nouvellement ajoutés
            self::addColumnToTable($tableName, $fieldName, "$fieldType$fieldSizePart $nullable");
        }
    }
    

    public static function addColumnFromConsole()
    {
        while (true) {
            echoGreen("Nom de la table : \n");
            $tableName = trim(fgets(STDIN));

            if (!self::tableExists($tableName)) {
                echoRed("La table '$tableName' n'existe pas. Veuillez vérifier le nom de la table et réessayer.\n");
                continue;
            }

            echoGreen("Nom de la colonne à ajouter : \n");
            $columnName = trim(fgets(STDIN));

            if (self::columnExists($tableName, $columnName)) {
                echoRed("Le nom de la colonne '$columnName' est déjà utilisé dans la table '$tableName'. Veuillez choisir un autre nom.\n");
                continue;
            }

            if ($columnName === 'id') {
                echoRed("Vous ne pouvez pas utiliser 'id' comme nom de colonne. Veuillez choisir un autre nom.\n");
                continue;
            }

            echoGreen("Type de la colonne (INT, VARCHAR, TEXT, DATE, DATETIME, BOOLEAN, FLOAT, DOUBLE, DECIMAL) : \n");
            $columnType = trim(fgets(STDIN));

            $validTypes = array(
                'INT',
                'VARCHAR',
                'TEXT',
                'DATE',
                'DATETIME',
                'BOOLEAN',
                'FLOAT',
                'DOUBLE',
                'DECIMAL'
            );

            if (!in_array(strtoupper($columnType), $validTypes)) {
                echoRed("Type de colonne non valide. Veuillez choisir parmi les types suivants : " . implode(', ', $validTypes) . "\n");
                continue;
            }

            echoGreen("La colonne '$columnName' peut-elle être NULL ? (oui/non) : \n");
            $nullableInput = trim(fgets(STDIN));
            $nullable = strtolower($nullableInput) === 'oui' ? 'NULL' : 'NOT NULL';

            $fieldSizePart = '';
            if ($columnType === 'VARCHAR' || $columnType === 'INT') {
                echoGreen("Taille du champ (laissez vide pour la valeur par défaut) : \n");
                $fieldSize = trim(fgets(STDIN));
                $fieldSizePart = !empty($fieldSize) ? "($fieldSize)" : "";
            }

            $columnDefinition = "$columnType$fieldSizePart $nullable";

            self::addColumnToTable($tableName, $columnName, $columnDefinition);
            break;
        }
    }

    public static function displayHelp()
{
    echoMagenta("Créer une table : table \n ");
    echoMagenta("Ajouter une colonne dans une table : add_column \n ");
    echoMagenta("Supprimer une colonne dans une table : sup_column \n ");
    echoMagenta("Afficher toutes les tables : list \n ");
    echoMagenta("Exporter la base de données : export_bdd \n ");
    echoMagenta("Exporter une table : export_table \n ");
    echoMagenta("Supprimer une table : sup_table \n ");
    echoMagenta("Fermeture de la console : quit \n ");
}

public static function displayTables()
{
    $tables = self::getAllTables();
    if (!empty($tables)) {
        echoGreen("Tables créées dans la base de données :\n");
        foreach ($tables as $table) {
            echo "$table\n";
        }
    } else {
        echoRed("Aucune table n'a été trouvée dans la base de données.\n");
    }
}

public static function deleteTable($tableName)
{
    while (true) {
        if (self::tableExists($tableName)) {
            $result = self::dropTable($tableName);
            if ($result) {
                echoMagenta("La table '$tableName' a été supprimée avec succès.\n");
            } else {
                echoRed("Erreur lors de la suppression de la table '$tableName'.\n");
            }
            break; 
        } else {
            echoRed("La table '$tableName' n'existe pas.\n");
            echoGreen("Nom de la table à supprimer : \n");
            $tableName = trim(fgets(STDIN));
        }
    }
}



public static function exportTableFromConsole()
{
    $tableName = '';

    while (true) {
        echoGreen("Nom de la table à exporter : \n");
        $tableName = trim(fgets(STDIN));

        if (self::tableExists($tableName)) {
            self::exportTable($tableName);
            return;
        } else {
            echoRed("La table '$tableName' n'existe pas. Veuillez vérifier le nom de la table et réessayer.\n");
        }
    }
}


public static function deleteColumnFromConsole()
{
    $tableName = '';

    while (true) {
        echoGreen("Nom de la table : \n");
        $tableName = trim(fgets(STDIN));

        if (self::tableExists($tableName)) {
            break;
        } else {
            echoRed("La table '$tableName' n'existe pas. Veuillez vérifier le nom de la table et réessayer.\n");
        }
    }

    $columns = self::getColumnNames($tableName);

    if (count($columns) == 1) {
        echoRed("Impossible de supprimer une colonne de cette table. Une table doit avoir au moins deux colonne.\n");
        echoRed("Utilisez 'sup_table' pour supprimer la table '$tableName'.\n");
        return;
    }

    while (true) {
        echoGreen("Nom de la colonne à supprimer : \n");
        $columnName = trim(fgets(STDIN));

        if (!self::columnExists($tableName, $columnName)) {
            echoRed("La colonne '$columnName' n'existe pas dans la table '$tableName'. Veuillez vérifier le nom de la colonne et réessayer.\n");
        } else {
            self::dropColumnFromTable($tableName, $columnName);
            break;
        }
    }
}

public static function getColumnNames($tableName)
{
    $connection = self::getConnection();
    $columns = array();

    $sql = "SHOW COLUMNS FROM $tableName";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    }

    $connection->close();

    return $columns;
}



}
