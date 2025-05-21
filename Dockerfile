# Utilise l'image officielle PHP 8.2 avec Apache
FROM php:8.2-apache

# Active mod_rewrite (utile pour Laravel, .htaccess, etc.)
RUN a2enmod rewrite

# Installe les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Copie le fichier php.ini personnalisé (si tu en as un)
COPY php.ini /usr/local/etc/php/

# Copie le contenu du dossier courant dans le dossier racine d'Apache
COPY . /var/www/html/

# Fixe les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Définit le dossier de travail
WORKDIR /var/www/html

# Expose le port 80 (Render le fait automatiquement, mais explicite ici)
EXPOSE 80
