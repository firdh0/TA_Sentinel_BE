#!/bin/bash

/cloud_sql_proxy -instances=ta-sentinel-be:asia-southeast2:ta-sentinel-be=tcp:3306 &

# Replace the placeholder in Apache config with the actual port
sed -i "s/Listen 80/Listen 8080/" /etc/apache2/ports.conf
sed -i "s/:80>/:8080>/" /etc/apache2/sites-available/000-default.conf

docker-php-entrypoint apache2-foreground
