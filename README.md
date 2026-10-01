# Handyzubehör Shop

Boutique en ligne d'accessoires pour mobiles (dropshipping), pour le marché suisse. Laravel, paiement Stripe.

## Langues

Le site existe en allemand, français, italien et anglais (`/de`, `/fr`, `/it`, `/en`).
À la première visite, `/` redirige vers la langue du navigateur si elle est proposée, sinon vers l'anglais.
Le dernier choix de langue est mémorisé dans un cookie et prime sur le navigateur.

- Langues proposées : `config/app.php` (`supported_locales`)
- Détection : `app/Support/Locale.php`
- Traductions de l'interface : `lang/*.json`

## Démarrer en local

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Tests : `php artisan test`
