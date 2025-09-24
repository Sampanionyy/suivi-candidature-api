FROM php:8.2-apache

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    zip unzip git curl libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Activation du module rewrite d'Apache
RUN a2enmod rewrite

# Configuration d'Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

# Installation de Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copie des fichiers du projet
COPY . /var/www/html

# Définition du répertoire de travail
WORKDIR /var/www/html

# Nettoyage des caches Composer précédents
RUN rm -rf vendor composer.lock

# Installation des dépendances Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Installation du package Laravel Lang si nécessaire
RUN composer require laravel-lang/lang --no-interaction || true

# Publication de la configuration CORS
RUN php artisan config:publish cors --no-interaction || true

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Génération de la clé d'application Laravel (si nécessaire)
RUN php artisan key:generate --no-interaction --force || true

# Clear des caches
RUN php artisan config:clear || true
RUN php artisan cache:clear || true

COPY . .

# Exposition du port
EXPOSE 80