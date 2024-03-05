<?php

require "./vendor/autoload.php";

use App\Model\ConsoleModel;

function echoGreen($text)
{
    echo "\033[32m$text\033[0m";
}

function echoBlue($text)
{
    echo "\033[34m$text\033[0m";
}

function echoRed($text)
{
    echo "\033[31m$text\033[0m";
}

function echoMagenta($text)
{
    echo "\033[35m$text\033[0m";
}

$validCommand = false;
ConsoleModel::displayHelp();
echoBlue("Veuillez entrer votre commande : \n ");

while (!$validCommand) {
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $command = trim($line);

    if ($command == 'table') {
        ConsoleModel::createTableFromConsole();
        echoBlue("Veuillez entrer votre commande : \n ");
    } elseif ($command == 'help') {
        ConsoleModel::displayHelp();
        echoBlue("Veuillez entrer votre commande : \n ");
    } elseif ($command == 'list') {
        ConsoleModel::displayTables();
        echoBlue("Veuillez entrer votre commande : \n ");
    } elseif ($command == 'sup_table') {
        echoGreen("Nom de la table à supprimer : \n");
        $tableName = trim(fgets(STDIN));
        ConsoleModel::deleteTable($tableName);
        echoBlue("Veuillez entrer votre commande : \n ");
    } elseif ($command == 'export_bdd') {
        ConsoleModel::exportDatabase();
        echoBlue("Veuillez entrer votre commande : \n ");
    } elseif ($command == 'export_table') {
        ConsoleModel::exportTableFromConsole();
        echoBlue("Veuillez entrer votre commande : \n ");
    } elseif ($command == 'add_column') {
        ConsoleModel::addColumnFromConsole();
        echoBlue("Veuillez entrer votre commande : \n ");
    } elseif ($command == 'sup_column') {
        ConsoleModel::deleteColumnFromConsole();
        echoBlue("Veuillez entrer votre commande : \n ");
    } elseif ($command == 'quit') {
        echoBlue("Fermeture de la console... \n");
        exit;
    } else {
        echoRed("Commande introuvable. Utilisez 'help' pour obtenir de l'aide.\n");
        echoBlue("Veuillez entrer votre commande : \n ");
    }
}
