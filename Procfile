web: vendor/bin/heroku-php-apache2 public/
worker: php artisan queue:work --queue=high,low,default --sleep=3 --tries=3
release: php artisan queue:restart
