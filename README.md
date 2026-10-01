# Handyzubehör Shop

Boutique en ligne d'accessoires pour mobiles en dropshipping, pour la Suisse et le Liechtenstein.
Laravel 13, back-office Filament, paiement Stripe Checkout, fournisseur CJ Dropshipping.

## Ce que fait le site

- **4 langues** (`/de`, `/fr`, `/it`, `/en`). `/` redirige vers la langue du navigateur, sinon l'anglais. Le choix fait sur le site est mémorisé.
- **Catalogue** : catégories, produits avec variantes (couleur, modèle…), compatibilité par modèle de téléphone, recherche, filtre « Mon téléphone ».
- **Panier** et **paiement** sur la page sécurisée de Stripe (TWINT, cartes, Apple Pay, Google Pay), prix en CHF TVA incluse, codes promo Stripe.
- **Commandes** : à la réception du paiement (webhook Stripe), la commande est marquée payée, le client reçoit un e-mail dans sa langue et la commande est envoyée automatiquement à CJ.
- **Suivi** : toutes les heures, les numéros de suivi sont récupérés chez CJ et le client reçoit un e-mail d'expédition.
- **Stock et prix** : toutes les 6 heures, prix d'achat et stock sont synchronisés avec CJ. Prix de vente = prix CJ (USD) × taux CHF × marge × TVA, arrondi à ,90 (sauf si « Garder ce prix » est coché).
- **Back-office** (`/admin`, en français) : commandes (envoyer à CJ, marquer expédiée, rembourser), produits (import CJ, traductions, prix, stock), catégories, modèles de téléphone.
- **Pages légales** : Impressum, CGV, protection des données (nLPD), livraison et retours, dans les 4 langues.

## Installation locale

```sh
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --seed               # catégories et modèles de téléphone
php artisan db:seed --class=DemoSeeder   # optionnel : 3 produits de démonstration
php artisan shop:make-admin vous@exemple.ch   # crée le compte admin (relancer pour changer le mot de passe)
php artisan serve
```

Tests : `php artisan test`

## Mise en production

1. **Hébergement** : PHP 8.3+, MySQL 8 (ou PostgreSQL), HTTPS. Laravel Forge + un VPS (Hetzner, Infomaniak…) ou Laravel Cloud conviennent.
2. **`.env`** : `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://votre-domaine.ch`, base de données, envoi d'e-mails (`MAIL_*`, par ex. Postmark ou Brevo) et les variables `SHOP_*`, `STRIPE_*`, `CJ_*` (voir `.env.example`).
3. **Tâches de fond** (obligatoires) :
   - un worker de file d'attente : `php artisan queue:work` (e-mails, envoi des commandes à CJ) ;
   - le planificateur, chaque minute : `* * * * * php artisan schedule:run`.
4. **Stripe** :
   - clés API dans `STRIPE_SECRET` ;
   - webhook vers `https://votre-domaine.ch/stripe/webhook` avec les événements `checkout.session.completed`, `checkout.session.async_payment_succeeded`, `checkout.session.async_payment_failed`, `checkout.session.expired`, `charge.refunded` ; son secret dans `STRIPE_WEBHOOK_SECRET` ;
   - activer **TWINT** dans Paramètres › Moyens de paiement ;
   - codes promo : à créer dans Stripe (Produits › Coupons), ils sont acceptés automatiquement.
   - En local : `stripe listen --forward-to localhost:8000/stripe/webhook`.
5. **CJ Dropshipping** : clé API dans `CJ_API_KEY`. Vérifier le nom du transporteur (`CJ_LOGISTIC_NAME`) et l'entrepôt de départ (`CJ_FROM_COUNTRY`) disponibles pour la Suisse, et approvisionner le solde CJ pour que les commandes soient payées.
6. **Contenu légal** : remplir `SHOP_COMPANY_*` (nom, adresse, e-mail, n° IDE/TVA) et faire relire les textes dans `resources/views/pages/` (ce sont des modèles).

## Ajouter des produits

Back-office › Produits › **Importer depuis CJ**, coller un ou plusieurs PID CJ.
Les produits importés sont **masqués** : compléter les traductions (DE, FR, IT), la catégorie, les modèles compatibles, vérifier le prix, puis cocher « Visible dans la boutique ».
En ligne de commande : `php artisan shop:import <pid> <pid>…`

## Organisation du code

| Sujet | Fichiers |
|---|---|
| Langues | `config/app.php` (`supported_locales`), `app/Support/Locale.php`, `app/Http/Middleware/SetLocale.php`, `lang/*/` |
| Boutique | `routes/web.php`, `app/Http/Controllers/Shop/`, `resources/views/shop/` |
| Panier, prix | `app/Support/Cart.php`, `app/Support/Pricing.php`, `config/shop.php` |
| Paiement | `app/Payments/`, `app/Http/Controllers/StripeWebhookController.php` |
| Fournisseur | `app/Suppliers/CjDropshipping.php`, `app/Actions/ImportSupplierProduct.php`, `app/Jobs/SendOrderToSupplier.php`, `app/Console/Commands/` |
| E-mails | `app/Mail/`, `resources/views/mail/` |
| Back-office | `app/Filament/` |
| Pages légales | `resources/views/pages/{de,fr,it,en}/` |
