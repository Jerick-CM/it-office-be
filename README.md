
## store laravel sail command in wsl linux
nano ~/.bash_aliases
add to file alias sail='bash vendor/bin/sail'
exit
source ~/.bash_aliases


or ./vendor/bin/sail up

##  Show route list in laravel 

php artisan route:list 

## rollback migration
php artisan migrate:rollback
php sail artisan migrate:refresh --seed


# artisan commands


# seeder 


# factory


# sail 


# sanctum


