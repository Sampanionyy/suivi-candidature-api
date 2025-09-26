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

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Génération de la clé d'application Laravel
RUN php artisan key:generate --no-interaction --force || true

# Clear des caches
RUN php artisan config:clear || true
RUN php artisan cache:clear || true

# Exposer le port dynamique pour Railway
EXPOSE ${PORT}

# Commande de démarrage Apache en foreground
CMD ["apache2-foreground"]
