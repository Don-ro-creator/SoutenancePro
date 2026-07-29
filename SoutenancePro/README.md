# SoutenancePro – Gestion des Soutenances de Fin d'Études

Application Symfony complète pour automatiser la gestion des soutenances de fin d'études.

## Fonctionnalités

- Authentification (Admin / Enseignant)
- CRUD Étudiants (recherche par nom, email unique)
- CRUD Enseignants (création de compte de connexion optionnelle)
- CRUD Salles (code unique, capacité > 0)
- CRUD Soutenances avec contrôles anti-conflit (salle + enseignants)
- Tableaux de bord adaptés au rôle
- Menus dynamiques selon le rôle

## Prérequis

- PHP 8.2+
- Composer
- Extension PHP : pdo_sqlite 
- Base de donées : SQLite

## Installation rapide


# 1. Créer le projet Symfony
composer create-project symfony/skeleton:"7.2.*" SoutenancePro
cd SoutenancePro

# 2. Installer les dépendances nécessaires
composer require webapp doctrine symfony/security-bundle form validator twig


# 4. Configurer la base de données (SQLite recommandé pour démarrer) dans le fichier .env

DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"

# 5. Créer le schéma
php bin/console doctrine:schema:update --force

php bin/console app:create-admin

php -S localhost:8000 -t public

Ouvrir http://127.0.0.1:8000 et se connecter avec l'admin créé.

Ce que j'ai crée, c'est ceci :

``` Compte administrateur 
        Admin : admin@soutenance.local
        Nom : ATOKOU
        Prénom : Romeo
        M2passe : romeo123 
```

## Base de données
SQLite 



