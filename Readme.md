Readme

# Thêatre 

Ce projet est une application permettant d'enregistrer des événements.

## Instructions pour démarrer le projet

### Prérequis
Avant d'exécuter ce projet, assurez-vous d'avoir installé :
 - PHP 8+
 - MySQL ou MariaDB (pour la base de données)
 - Composer (gestionnaire de dépendances PHP)
 - Un serveur local (Symfony Server, CLI, XAMPP, WAMP ou MAMP)
 - (Optionnel) Node.js et npm si vous gérez des assets frontend.
 - (Optionnel) Stripe CLI ou un compte Stripe pour le module de paiement.

### Technologies utilisées

 - Symfony (framework PHP)
 - Bootstrap (via CDN)
 - Leaflet.js (pour la carte OpenStreetMap)
 - Stripe (paiement en ligne)
 - Symfony Mailer (envoi d’e-mails de confirmation)
 - 
 - 

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

Puis, créez la base de données et exécutez les migrations :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 4. (Facultatif) Installer/Compiler les assets 
Si votre projet utilise des assets via npm ou Webpack Encore :
```bash
npm install
npm run dev  # Pour le mode développement
npm run build  # Pour le mode production
```

### 5. Lancer le serveur Symfony
Avec la Symfony CLI :
```bash
symfony serve
```
Ou bien via PHP intégré :
```bash
php -S 127.0.0.1:8000 -t public
```
Accédez ensuite à http://127.0.0.1:8000 pour voir la page d'accueil.


## Fonctionnalités Ajoutées

### 1. Page d'acceuil

#### 1.1. Carrousel d'Images
 - Situé en haut de la page.
 - Contient quatre images.
 - Transition fluide entre les images.

#### 1.2. Cartes Cadeaux
 - Affichées sous le carrousel.
 - Présentées sous forme de Bootstrap Cards.

#### 1.3. Carte OpenStreetMap
 - Intégrée via Leaflet.js.
 - Un marqueur indique la localisation du théâtre.
 - Une info-bulle affiche nom, adresse et lien Google Maps.

#### 1.4.Evenement
 - L’application gère plusieurs événements de théâtre (entité Evenement).
 - Possibilité de créer, modifier, supprimer et afficher ces événements.
 - Chaque événement peut inclure un titre, une description, une date, un prix, etc.

### 2. Système de Paiement (Stripe)
#### 2.1. Installation Stripe
```bash
composer require stripe/stripe-php
```
Dans votre fichier .env, ajoutez (ou mettez à jour) vos clés Stripe :

STRIPE_PUBLIC_KEY="pk_test_votre_cle_publique"
STRIPE_SECRET_KEY="sk_test_votre_cle_secrete"

Obtenez ces clés depuis votre Dashboard Stripe.

#### 2.2. Entité Payment
Une entité Payment est utilisée pour stocker chaque transaction (utilisateur, montant, statut, etc.). Assurez-vous de l’avoir générée, puis exécutez :
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```
#### 2.3. Contrôleur PaymentController
Un contrôleur PaymentController gère :
  - La création d’une session de paiement (Stripe Checkout),
  - La redirection vers Stripe,
  - Le retour en cas de succès ou d’annulation,
  - (Optionnel) Le webhook Stripe pour valider le paiement sur votre serveur.

Vous trouverez un exemple de code dans src/Controller/PaymentController.php (voir tutoriel d’intégration complet dans la documentation interne).

#### 2.4. Bouton “Payer”
Dans la vue reservation (ex. templates/reservation/reservation.html.twig), ajoutez :

twig
<a class="btn btn-primary"
   href="{{ path('payment_create', {'id': evenement.id}) }}">
   Payer maintenant
</a>

Le bouton redirige vers la route qui crée la session Stripe et envoie l’utilisateur sur la page de paiement.

### 3. Envoi d’e-mail de confirmation
Grâce à Symfony Mailer, un e-mail de confirmation est envoyé après un paiement réussi.
   - Installez (si besoin) : composer require symfony/mailer
   - Configurez la variable MAILER_DSN dans .env (ex : smtp://localhost:1025 pour un serveur local).
   - Dans la méthode paymentSuccess(), injectez MailerInterface et envoyez votre e-mail (voir PaymentController).


## Extensions Recommandées pour VSCode

 - PHP Intelephense (auto-complétion PHP)

 - Symfony Support (support des fichiers Symfony)

 - PHP Debug (débogage)

 - Twig Language 2 (support Twig)


## Tests

Pour exécuter les tests unitaires (si configurés) :
```bash
php bin/phpunit
```

## Auteur

👨‍💻 Développé par Pascal OURSEL - 
[GitHub](https://github.com/Pascal-OU)