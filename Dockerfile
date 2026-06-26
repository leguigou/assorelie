FROM php:8.2-apache

# Activer mod_rewrite pour les URLs propres
RUN a2enmod rewrite

# Activer mod_headers pour les en-têtes de sécurité et cache
RUN a2enmod headers

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install -j$(nproc) pdo_mysql

# Configurer Apache pour le rewrite
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copier les fichiers de l'application
COPY . /var/www/html/

# Créer le dossier data avec les bons droits pour l'écriture (formulaire)
RUN chown -R www-data:www-data /var/www/html/data && \
    chmod 755 /var/www/html/data

# Configuration du VirtualHost
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html\n\
    <Directory /var/www/html>\n\
        Options -Indexes +FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]
