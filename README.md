# WoodyCraft – Site e‑commerce de puzzles 3D (Laravel)

Un projet d’e‑commerce permettant de parcourir des catégories, consulter des puzzles 3D, gérer un panier et passer commande (paiement par chèque ou redirection PayPal), avec authentification obligatoire pour finaliser l’achat.

---

## Sommaire

* [Aperçu](#aperçu)
* [Fonctionnalités](#fonctionnalités)
* [Stack technique](#stack-technique)
* [Prérequis](#prérequis)
* [Installation et démarrage](#installation-et-démarrage)
* [Configuration de l’environnement](#configuration-de-lenvironnement)
* [Base de données & seeders](#base-de-données--seeders)
* [Commandes utiles](#commandes-utiles)
* [Structure des routes](#structure-des-routes)
* [Comptes de test (optionnel)](#comptes-de-test-optionnel)
* [Génération PDF facture (chèque)](#génération-pdf-facture-chèque)
* [Bonnes pratiques & CI/CD](#bonnes-pratiques--cicd)
* [Roadmap / US supplémentaires](#roadmap--us-supplémentaires)
* [Licence](#licence)

---

## Aperçu

WoodyCraft vend des puzzles 3D. L’objectif de l’application est d’offrir une expérience d’achat simple :

* Page d’accueil listant les **catégories**
* Pages listant les **puzzles** d’une catégorie
* **Fiche produit**
* **Authentification** (requise pour commander)
* **Passage de commande** avec saisie d’**adresse de livraison**
* **Paiement** au choix : **chèque (avec facture PDF)** ou **redirection PayPal**

## Fonctionnalités

* Visualiser la liste des **catégories**
* Visualiser la liste des **produits par catégorie**
* Visualiser les **détails d’un produit**
* **Ajouter un produit** au panier depuis la page produit (et/ou depuis les listes)
* **Modifier la quantité** d’un produit du panier
* **Supprimer** un produit du panier
* **Se connecter / s’inscrire**
* **Passer commande**
* **Saisir / modifier** l’**adresse de livraison** (réutilisée par défaut lors d’une nouvelle commande)
* Choix du paiement :

  * **Chèque** → **facture PDF** générée (détails + total + adresse d’envoi du chèque)
  * **PayPal** → redirection vers la page officielle
  * **Carte** 
> Des idées d’extensions : multilingue, administration, produits similaires, etc.

## Stack technique

* **PHP** >= 8.x
* **Laravel** 10/11 (artisan, migrations, seeders)
* **MySQL/MariaDB** laragon
* **Blade** / **Tailwind CSS** (ou un framework CSS de votre choix)

## Prérequis

* PHP 8.x, Composer 2.x
* Node.js 18+ & npm
* MySQL/MariaDB (ou compatible)

## Installation et démarrage

```bash
# 1) Cloner le dépôt
git clone <URL_DU_REPO>
cd woodycraft

# 2) Installer les dépendances PHP
composer install

# 3) Dupliquer le fichier d’environnement
cp .env.example .env

# 4) Générer la clé d’application
php artisan key:generate

# 5) Configurer la connexion DB dans .env
# DB_DATABASE=woodycraft
# DB_USERNAME=root
# DB_PASSWORD=secret

# 6) Lancer les migrations (+ seeders si dispo)
php artisan migrate --seed

# 7) Lier le stockage (pour images, PDF, etc.)
php artisan storage:link

# 8) Installer les dépendances front & builder
npm install
npm run build   # ou: npm run dev

# 9) Démarrer le serveur local
php artisan serve
```

## Configuration de l’environnement

Dans le fichier `.env` :

```dotenv
APP_NAME="WoodyCraft"
APP_ENV=local
APP_KEY=base64:...
APP_URL=http://127.0.0.1:8000

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=woodycraft
DB_USERNAME=root
DB_PASSWORD=secret

# PayPal (redirection simple)
PAYMENT_PROVIDER=paypal
PAYPAL_REDIRECT_URL=https://www.paypal.com/fr/home

# PDF / Factures
INVOICE_OUTPUT_DISK=public     # ou local
INVOICE_OUTPUT_PATH=invoices
```

> Adaptez les variables aux besoins (disque de stockage, dossier de sortie, etc.). Pour un vrai paiement PayPal/CB, utilisez le SDK/API officiel et ajoutez les credentials.

## Base de données & seeders

* Modélisez les **catégories**, **puzzles**, **paniers**, **relations N:N**, **commandes**, **adresses**, **lignes de commande**, **utilisateurs**.

```bash
php artisan migrate --seed
# ou, selon vos seeders :
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=PuzzleSeeder
```

> Si vous avez une table pivot sans timestamps, veillez à **désactiver** `withTimestamps()` côté relation Eloquent ou à **ajouter** les colonnes `created_at`/`updated_at` si vous les utilisez.

## Commandes utiles

```bash
# Lister les routes
php artisan route:list

# Vider caches (après modifs config/routes/views)
php artisan optimize:clear

# Vérifier les erreurs dans les logs
tail -f storage/logs/laravel.log
```

## Structure des routes

Routes publiques principales :

* `GET /` → liste des catégories
* `GET /categories` → catégories
* `GET /categories/{categorie}` → produits de la catégorie
* `GET /puzzles` → catalogue
* `GET /puzzles/{puzzle}` → fiche produit

Routes protégées (auth requise) :

* `GET /cart` / `POST /cart/add` / `PATCH /cart/{id}` / `DELETE /cart/{id}`
* `GET /checkout/address` / `POST /checkout/address`
* `GET /checkout/payment` (choix chèque / PayPal)
* `GET /checkout/confirmation`
* `GET /checkout/invoice` (facture PDF)

> Adaptez selon vos contrôleurs et noms de routes réels (`route:list`).

## Comptes de test (optionnel)

Vous pouvez fournir des identifiants de test dans ce README (ou en variables d’environnement) :

* **Client** : `client@example.com` / `password`
* **Admin** (si back‑office prévu) : `admin@example.com` / `password`

## Génération PDF facture (chèque)

Lors du choix **Paiement par chèque** :

* Générer un **PDF de facture** (liste des produits, quantités, prix unitaires, **montant total**).
* Inclure dans le PDF l’**adresse d’envoi du chèque** et les **références de commande**.
* Sauvegarder le PDF dans `storage/app/public/invoices/` (ou le disque configuré) et proposer le **téléchargement** depuis la page de confirmation.

> Vous pouvez utiliser un package type **barryvdh/laravel-dompdf** ou **laravel-snappy**. Pensez à tester l’encodage (UTF‑8) et les formats A4.

## Bonnes pratiques & CI/CD

* **Branches Git** : `main` (stable), `develop`, features `feat/*`, fix `fix/*`.
* **PSR-12**, **PHPStan/Pint** pour la qualité du code.
* **Tests** (Feature & Unit) pour les workflows critiques : ajout panier, commande, génération PDF.
* **Intégration continue** (GitHub Actions/GitLab CI) : lint, tests, build.

## Roadmap / US supplémentaires

* **Multilingue** (ex : FR/EN avec Laravel Localization)
* **Administration** (CRUD catégories/puzzles, gestion commandes)
* **Produits similaires** sur la fiche produit
* Ajout au panier **depuis la liste** des produits

## Licence

Projet éducatif – BTS SIO SLAM 

# SP2_Api
