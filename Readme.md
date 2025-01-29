Readme

# Thêatre 

Ce projet est une application permettant d'enregistrer des événements.

## Instructions pour démarrer le projet

### Prérequis
Avant d'exécuter ce projet, assurez-vous d'avoir installé :

### Technologies utilisées

 - Symfony 
 - Bootstrap (via CDN)
 - Leaflet.js (pour la carte OpenStreetMap)
 - Composer (gestionnaire de dépendances PHP)
 - PHP 8+
 - MySQL ou MariaDB (pour la gestion de la base de données)
 - Un serveur local (Symfony Server, XAMPP, WAMP, ou MAMP)

### Installation de Symfony

Si Symfony n'est pas encore installé sur votre machine, utilisez la commande suivante :
```bash
curl -sS https://get.symfony.com/cli | bash
```

Ajoutez ensuite Symfony au PATH :

export PATH="$HOME/.symfony/bin:$PATH"


## Installation du Projet

### 1. Cloner le projet
```bash
git clone https://github.com/votre-repo/theatre.git
cd theatre
```

### 2. Installer les dépendances
```bash
composer install
```

### 3. Configurer la base de données
Modifiez le fichier .env avec les informations de connexion :

DATABASE_URL=mysql://root:password@127.0.0.1:3306/theatre

Puis, créez la base de données :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 4. Installer les assets (si applicable)
```bash
npm install
npm run dev  # Pour le mode développement
npm run build  # Pour le mode production
```

### 5. Lancer le serveur Symfony
```bash
symfony serve
```
Ou avec PHP :
```bash
php -S 127.0.0.1:8000 -t public
```
Accédez à http://127.0.0.1:8000 pour voir la page d'accueil.


## Fonctionnalités Ajoutées

### 1. Carrousel d'Images

Situé en haut de la page.
Contient quatre images.
Transition fluide entre les images.

### 2. Cartes Cadeaux
Affichées sous le carrousel.
Présentées sous forme de Bootstrap Cards.

### 3. Carte OpenStreetMap
Intégrée via Leaflet.js.
Un marqueur indique la localisation du théâtre.
Une info-bulle affiche nom, adresse et lien Google Maps.


## Extensions Recommandées pour VSCode

PHP Intelephense (auto-complétion PHP)

Symfony Support (support des fichiers Symfony)

PHP Debug (débogage)

Twig Language 2 (support Twig)


## Tests

Pour exécuter les tests unitaires (si configurés) :
```bash
php bin/phpunit
```

## Auteur

👨‍💻 Développé par Pascal OURSEL - 
[GitHub](https://github.com/Pascal-OU)