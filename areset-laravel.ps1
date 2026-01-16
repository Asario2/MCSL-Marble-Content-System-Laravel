# 1. Alten Vendor + composer.lock löschen
Remove-Item -Recurse -Force vendor
Remove-Item -Force composer.lock

# 2. Composer-Install mit maximalem Memory
php -d memory_limit=-1 D:\bin\composer install --no-scripts -vvv

php -d memory_limit=-1 D:\bin\composer dump-autoload

php artisan clear-compiled
php artisan optimize:clear
php artisan package:discover

