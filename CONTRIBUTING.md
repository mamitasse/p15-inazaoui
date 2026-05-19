# CONTRIBUTING.md

# Contribution au projet Ina Zaoui

Merci de contribuer au projet.

---

# Règles générales

* Respecter l’architecture Symfony du projet ;
* Utiliser des noms clairs pour les classes, méthodes et variables ;
* Écrire du code lisible et correctement indenté ;
* Tester les modifications avant chaque push.

---

# Installation du projet

```bash
git clone https://github.com/mamitasse/p15-inazaoui.git
cd p15-inazaoui
composer install
```

Configurer ensuite le fichier `.env.local`.

---

# Base de données

Créer la base :

```bash
php bin/console doctrine:database:create
```

Lancer les migrations :

```bash
php bin/console doctrine:migrations:migrate
```

Charger les fixtures :

```bash
php bin/console doctrine:fixtures:load
```

---

# Création d’une branche

Avant toute modification :

```bash
git checkout -b nom-de-la-branche
```

Exemple :

```bash
git checkout -b feature-pagination
```

---

# Vérifications avant commit

## Tests PHPUnit

```bash
php bin/phpunit
```

---

## Vérification Twig

```bash
php bin/console lint:twig templates
```

---

## Vérification YAML

```bash
php bin/console lint:yaml config
```

---

# Coverage PHPUnit

Pour générer le rapport de couverture :

```bash
php -d xdebug.mode=coverage bin/phpunit --coverage-html coverage
```

---

# Commits Git

Utiliser des messages explicites.

Exemples :

```bash
git commit -m "Ajout pagination invités"
```

```bash
git commit -m "Correction tests PHPUnit"
```

---

# Pull Requests

Avant une Pull Request :

* vérifier que tous les tests passent ;
* vérifier le lint Twig ;
* vérifier le lint YAML ;
* expliquer clairement les modifications réalisées.

---

# Contribution au code

Les contributions peuvent concerner :

* le back-end Symfony ;
* les templates Twig ;
* les performances ;
* les tests PHPUnit ;
* la documentation ;
* les GitHub Actions.

---

# Signalement de bugs

Lors du signalement d’un bug, préciser :

* les étapes pour reproduire ;
* le comportement observé ;
* le comportement attendu ;
* les éventuels messages d’erreur.

---

# Proposition de fonctionnalités

Les nouvelles fonctionnalités doivent :

* respecter l’architecture du projet ;
* inclure des tests si nécessaire ;
* être documentées.

---

# Documentation

Toute nouvelle fonctionnalité importante doit être ajoutée au README si nécessaire.
