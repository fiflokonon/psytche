# Utilisez l'image Debian 11 comme image de base
FROM debian:11

# Étiquette pour l'auteur (facultatif)
LABEL authors="Arnaud Fifonsi"

# Mise à jour du système et installation des paquets de base
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libmagickwand-dev \
    nodejs \
    npm

# Ajout du référentiel de packages PHP
RUN apt-get install -y lsb-release apt-transport-https ca-certificates wget
RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list

# Mise à jour des informations des paquets
RUN apt-get update

# Installation de PHP et des extensions PHP nécessaires
RUN apt-get install -y \
    php8.2 \
    php8.2-fpm \
    php8.2-mysql \
    php8.2-pdo-pgsql\
    php8.2-imagick\
    php8.2-curl
    
RUN apt-get update

RUN apt-get install -y php8.2-mbstring
# Installation de PECL
RUN apt-get install -y php-pear php-dev

# Installation de l'extension Redis pour PHP
RUN pecl install redis

# Créez le répertoire pour les fichiers de configuration PHP s'il n'existe pas
RUN mkdir -p /usr/local/etc/php/conf.d/

# Activation de l'extension Redis pour PHP en modifiant docker-php-ext-redis.ini
RUN echo "extension=redis.so" > /usr/local/etc/php/conf.d/docker-php-ext-redis.ini

# Supprimez les dépendances précédentes, le cache npm et le dossier .npm
RUN rm -rf node_modules
RUN rm -f package-lock.json
RUN rm -rf .npm
RUN npm cache clean --force

# Installation de curl
RUN apt update && apt install -y curl

# Installation de Composer (gestionnaire de dépendances PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Assurez-vous que Composer est exécutable
RUN chmod +x /usr/local/bin/composer

# Copiez le fichier package.json dans le répertoire de l'application
COPY package.json ./

# Installez les dépendances npm
#RUN npm install --no-cache

# Installez Laravel Echo Server de manière globale
RUN npm install -g laravel-echo-server

# Copiez le fichier de configuration Laravel Echo Server
COPY laravel-echo-server.json ./

# Copiez le script de démarrage personnalisé
COPY start.sh /start.sh

# Assurez-vous que le script est exécutable
RUN chmod +x /start.sh

# Définissez le répertoire de travail dans le conteneur
WORKDIR /var/www/html

# Copiez les fichiers de l'application Laravel dans le conteneur
COPY . .

# Installez les dépendances PHP avec Composer
RUN composer install

# Exposez le port 9000 (pour PHP-FPM)
EXPOSE 9000

# Exécutez le script de démarrage personnalisé
CMD ["/start.sh"]
