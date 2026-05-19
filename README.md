# README.md

# Ina Zaoui

Projet Symfony développé dans le cadre de la formation OpenClassrooms.

---

# Description

Cette application permet :

* d’afficher le portfolio d’Ina Zaoui ;
* de gérer les invités ;
* de gérer les albums ;
* de gérer les médias ;
* d’administrer le site via une interface sécurisée ;
* de bloquer ou débloquer des invités ;
* d’optimiser les performances de la page Invités grâce à la pagination ;
* de garantir la qualité du projet grâce aux tests PHPUnit.

---

# Pré-requis

Avant l’installation, vérifier que les outils suivants sont installés :

* PHP 8.2+
* Composer
* Symfony CLI
* MySQL ou MariaDB  XAMPP (Apache + MySQL)
* Git

---

# Installation

## 1. Cloner le projet

```bash 
git clone https://github.com/mamitasse/p15-inazaoui.git
```

---

## 2. Aller dans le dossier du projet

```bash 
cd p15-inazaoui
```

---

## 3. Installer les dépendances

Cette commande installe toutes les dépendances Symfony et PHP nécessaires au projet.

```bash 
composer install
```

---

# Démarrer les services

Avant de lancer le projet, démarrer :

- Apache
- MySQL

depuis le panneau de contrôle XAMPP.

```txt
XAMPP Control Panel
```

Les services doivent être en vert avant de continuer.

# Configuration

Créer un fichier `.env.local` à la racine du projet :

```env 
DATABASE_URL="mysql://root:@127.0.0.1:3306/p15_inazaoui?serverVersion=10.4.32-MariaDB"
```

Adapter les identifiants selon votre environnement local.

---

# Base de données

## Créer la base de données

Cette commande crée la base de données utilisée par Symfony.

```bash 
php bin/console doctrine:database:create
```

---

## Exécuter les migrations

Cette commande crée automatiquement les tables nécessaires au projet.

```bash 
php bin/console doctrine:migrations:migrate
```

---

## Charger les fixtures

Les fixtures permettent d’ajouter des données de démonstration dans la base de données.

```bash 
php bin/console doctrine:fixtures:load
```

---

# Lancer le projet

Cette commande démarre le serveur Symfony en local.

```bash 
symfony serve
```

Puis ouvrir :

```txt 
http://127.0.0.1:8000
```

---

# Authentification administrateur

Compte administrateur de démonstration :

```txt 
Email : ina@test.com
Mot de passe : password
```

---

# Fonctionnalités principales

* Authentification administrateur
* Gestion des invités
* Gestion des albums
* Gestion des médias
* Blocage / déblocage des invités
* Suppression sécurisée avec CSRF
* Portfolio public
* Pagination des invités
* Optimisation des performances
* Tests automatisés PHPUnit

---

# Tests

## Lancer tous les tests

Cette commande exécute tous les tests PHPUnit du projet afin de vérifier que l’application fonctionne correctement.

```bash 
php bin/phpunit
```

---

## Afficher le pourcentage de couverture des tests

Cette commande affiche le niveau de couverture des tests PHPUnit dans le terminal.

Elle permet de voir quelles parties du code sont testées.

```bash 
php -d xdebug.mode=coverage bin/phpunit --coverage-text
```

---

## Générer le rapport HTML de couverture

Cette commande génère un rapport HTML détaillé des tests.

Le rapport est créé dans le dossier `coverage/`.

```bash
php -d xdebug.mode=coverage bin/phpunit --coverage-html coverage
```

Le rapport HTML sera généré dans :

```txt 
coverage/
```

---

# Vérification de syntaxe

## Vérification des fichiers Twig

Cette commande vérifie que tous les templates Twig ne contiennent aucune erreur de syntaxe.

```bash 
php bin/console lint:twig templates
```

---

## Vérification des fichiers YAML

Cette commande vérifie que les fichiers de configuration YAML sont valides.

Elle permet d’éviter des erreurs de configuration Symfony.

```bash 
php bin/console lint:yaml config
```

---

# Optimisation des performances

La page Invités a été optimisée avec :

* pagination ;
* utilisation d’un OFFSET ;
* limitation du nombre de résultats ;
* tri alphabétique des invités ;
* amélioration des requêtes Doctrine ;
* réduction du temps de chargement.

Les performances ont été analysées avec le Symfony Web Profiler.

---

# Données et images

Les données de démonstration sont générées grâce aux Doctrine Fixtures.

Les images doivent être placées dans :

Les images doivent être placées dans :

```txt
public/uploads/
```

Le fichier `backup.zip` a été retiré du dépôt afin d’éviter un poids excessif du projet.

---

# Technologies utilisées

* Symfony
* Doctrine ORM
* Twig
* PHPUnit
* Bootstrap
* MySQL / MariaDB

---

# Auteur
Idiri Tassadit
Projet réalisé dans le cadre de la formation OpenClassrooms.
