FROM php:8.2-apache

# Variables d'environnement Railway
ARG PORT
ENV PORT=${PORT:-8080}

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    zip unzip git curl libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Activation du module rewrite d'Apache
RUN a2enmod rewrite

# Configuration du DocumentRoot pour Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e "s/80/${PORT}/g" /etc/apache2/ports.conf \
    && sed -ri -e "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf

# Forcer ServerName pour supprimer le warning Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copier Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier les fichiers du projet
WORKDIR /var/www/html
COPY . .

# Nettoyage des caches Composer précédents
RUN rm -rf vendor composer.lock

# Installer les dépendances Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Installer Laravel Lang si nécessaire
RUN composer require laravel-lang/lang --no-interaction || true

# Publication de la config CORS (optionnel)
RUN php artisan config:publish cors --no-interaction || true

# CORRECTION : Créer les répertoires nécessaires et définir les permissions
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/bootstrap/cache

# Permissions correctes pour Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Créer le fichier de log avec les bonnes permissions
RUN touch /var/www/html/storage/logs/laravel.log \
    && chown www-data:www-data /var/www/html/storage/logs/laravel.log \
    && chmod 664 /var/www/html/storage/logs/laravel.log

# Génération de la clé d'application Laravel
RUN php artisan key:generate --no-interaction --force || true

# Clear des caches
RUN php artisan config:clear || true
RUN php artisan cache:clear || true

# Attendre que la DB soit disponible et exécuter les migrations
RUN php artisan migrate:status || true
RUN php artisan migrate --force || echo "Migration failed, continuing..."

# Forcer la création de la table sessions si elle n'existe pas
RUN php -r "
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
if (!Schema::hasTable('sessions')) {
    Schema::create('sessions', function (Blueprint \$table) {
        \$table->string('id')->primary();
        \$table->foreignId('user_id')->nullable()->index();
        \$table->string('ip_address', 45)->nullable();
        \$table->text('user_agent')->nullable();
        \$table->longText('payload');
        \$table->integer('last_activity')->index();
    });
}
" || true

# Commande de démarrage personnalisée qui vérifie les migrations
COPY <<EOF /usr/local/bin/start-laravel.sh
#!/bin/bash
echo "=== DEBUG: Vérification de la base de données ==="
echo "Database Host: \$DB_HOST"
echo "Database Name: \$DB_DATABASE"

echo "=== Vérification des tables existantes ==="
php artisan tinker --execute="
\$tables = DB::select('SHOW TABLES');
foreach (\$tables as \$table) {
    echo 'Table: ' . array_values((array)\$table)[0] . PHP_EOL;
}
"

echo "=== Vérification spécifique de la table sessions ==="
php artisan tinker --execute="
try {
    \$exists = Schema::hasTable('sessions');
    echo 'Table sessions exists: ' . (\$exists ? 'YES' : 'NO') . PHP_EOL;
    if (\$exists) {
        \$count = DB::table('sessions')->count();
        echo 'Sessions count: ' . \$count . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Error checking sessions table: ' . \$e->getMessage() . PHP_EOL;
}
"

echo "=== Statut des migrations ==="
php artisan migrate:status

echo "=== Configuration de session actuelle ==="
php artisan tinker --execute="
echo 'Session driver: ' . config('session.driver') . PHP_EOL;
echo 'Session connection: ' . config('session.connection') . PHP_EOL;
echo 'Session table: ' . config('session.table') . PHP_EOL;
"

echo "=== Tentative de création manuelle si nécessaire ==="
php artisan tinker --execute="
if (!Schema::hasTable('sessions')) {
    echo 'Creating sessions table manually...' . PHP_EOL;
    Schema::create('sessions', function (\$table) {
        \$table->string('id')->primary();
        \$table->foreignId('user_id')->nullable()->index();
        \$table->string('ip_address', 45)->nullable();
        \$table->text('user_agent')->nullable();
        \$table->longText('payload');
        \$table->integer('last_activity')->index();
    });
    echo 'Sessions table created successfully!' . PHP_EOL;
} else {
    echo 'Sessions table already exists.' . PHP_EOL;
}
"

echo "=== Démarrage d'Apache ==="
exec apache2-foreground
EOF

RUN chmod +x /usr/local/bin/start-laravel.sh

# Exposer le port dynamique pour Railway
EXPOSE ${PORT}

# Commande de démarrage avec vérification des migrations
CMD ["/usr/local/bin/start-laravel.sh"]