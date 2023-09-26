#!/bin/bash
# Naviguez vers le répertoire de l'application Laravel
cd /var/www/html || exit
# Modifier la variable DB_HOST dans le fichier .env
sed -i 's#DB_HOST=.*#DB_HOST=db#' /var/www/html/.env

# Exécutez les migrations Laravel
php artisan key:generate
php artisan migrate --seed
php artisan optimize:clear
# Démarrez le serveur Laravel
php artisan serve --host=0.0.0.0 --port=8000

# Démarrer PHP-FPM en arrière-plan Démarrer Laravel Echo Server en arrière-plan
php-fpm & laravel-echo-server start
# Garder le conteneur en cours d'exécution
tail -f /dev/null
