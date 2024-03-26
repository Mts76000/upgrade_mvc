# Documentation de la Console MVC

La Console MVC est un outil en ligne de commande permettant de gérer les tables et les colonnes d'une base de données.


## Prérequis
- PHP >= 7.4.*
- Composer
- Node - npm
- MySQL avec PDO

## Installation

```bash
  git clone https://github.com/Mts76000/upgrade_mvc.git
  cd mvc6
  composer dump-autoload 
  npm install
```


### Le fichier de configuration

Pour démarrer copier-coller le contenu du fichier config/config-dist.php dans un nouveau fichier config.php
```php
/* config/config.php */
return array(
    'db_name'   => 'dbname',
    'db_user'   => 'root',
    'db_pass'   => '',
    'db_host'   => 'localhost',
    
    'version' => '1.0.0'
);
```
## Serveur php & Webpack
```bash
// Pour lancer serveur PHP
php -S localhost:2323 -t public
// Pour lancer Webpack
npm run watch
// Pour build Webpack
npm run build
```


## Commandes Disponibles

### 1. Créer une Table

La commande `table` permet de créer une nouvelle table dans la base de données.

### 2. Ajouter une Colonne

La commande `add_column` permet d'ajouter une nouvelle colonne à une table existante.

### 3. Supprimer une Colonne

La commande `sup_column` permet de supprimer une colonne d'une table existante.

### 4. Afficher toutes les Tables

La commande `view_table` permet d'afficher une table en detail.


### 5. Afficher le detail d'un table

La commande `list` permet d'afficher la liste de toutes les tables présentes dans la base de données.

### 6. Exporter la Base de Données

La commande `export_bdd` permet d'exporter toute la base de données dans un fichier SQL.

### 7. Exporter une Table

La commande `export_table` permet d'exporter le contenu d'une table spécifique dans un fichier SQL.

### 8. Supprimer une Table

La commande `sup_table` permet de supprimer une table existante de la base de données.

### 9. Aide

La commande `help` affiche l'aide et la liste des commandes disponibles.

## Fichiers Modifiés

- `ConsoleModel.php`
- `Console.php`



---
