#!/usr/bin/env bash

set -e

role=${CONTAINER_ROLE:-app}
user=${DOCKER_USER}

#env=${APP_ENV:-production}
#
#if [ "$env" != "local" ]; then
#    echo "Caching configuration..."
#    (cd /var/www && php artisan config:cache && php artisan route:cache && php artisan view:cache)
#fi

composer install --no-interaction --optimize-autoloader
npm install && npm run build
php artisan storage:link

if [ "$role" = "app" ]; then
    echo "App role"
    exec php-fpm -F
elif [ "$role" = "queue" ]; then
    echo "Queue role"
    exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
elif [ "$role" = "scheduler" ]; then
    echo "Scheduler role"
    chown www-data.www-data /var/www/docker-compose/scheduler.sh
    chmod u+x /var/www/docker-compose/scheduler.sh
#    crontab -u www-data -l | { cat; echo "* * * * * /usr/local/bin/php /var/www/artisan schedule:run >> /var/www/storage/logs/cron.log 2>&1"; } | crontab -u www-data -
    echo "* * * * * /var/www/docker-compose/scheduler.sh" | crontab -u www-data -
    crontab -u www-data -l
    exec cron -f
else
    echo "Could not match the container role \"$role\""
    exit 1
fi
