FROM php:8.2-apache

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    zip unzip git curl libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Activation du module rewrite d'Apache
RUN a2enmod rewrite

# Configuration du DocumentRoot pour Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

# Forcer ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copier Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier les fichiers du projet
WORKDIR /var/www/html
COPY . .

# Installer les dépendances Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Créer les répertoires nécessaires
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} \
             storage/app/public/profile_photos bootstrap/cache

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

# Exécuter les migrations automatiquement
# RUN php artisan migrate --force || true

EXPOSE 8080

# Commande de démarrage Apache en foreground
CMD ["apache2-foreground"]