# Documentation de la Console MVC

La Console MVC est un outil en ligne de commande permettant de gérer les tables et les colonnes d'une base de données.

## Commandes Disponibles

### 1. Créer une Table

La commande `create_table` permet de créer une nouvelle table dans la base de données.

### 2. Ajouter une Colonne

La commande `add_column` permet d'ajouter une nouvelle colonne à une table existante.

### 3. Supprimer une Colonne

La commande `sup_column` permet de supprimer une colonne d'une table existante.

### 4. Afficher toutes les Tables

La commande `list` permet d'afficher la liste de toutes les tables présentes dans la base de données.

### 5. Exporter la Base de Données

La commande `export_bdd` permet d'exporter toute la base de données dans un fichier SQL.

### 6. Exporter une Table

La commande `export_table` permet d'exporter le contenu d'une table spécifique dans un fichier SQL.

### 7. Supprimer une Table

La commande `sup_table` permet de supprimer une table existante de la base de données.

### 8. Aide

La commande `help` affiche l'aide et la liste des commandes disponibles.

## Fichiers Modifiés

- `ConsoleModel.php`: Ce fichier contient les méthodes pour gérer les opérations sur les tables et les colonnes de la base de données via la console.
- `Console.php`: Ce fichier contient toutes les commandes et les appels aux fonctions.

---
