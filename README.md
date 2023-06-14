Requirements:
1. PHP 8.2
2. composer 2.5.1
2. Mysql server 8


Please run the following commands after you cloned successfully.

Steps:

1. composer install
2. Fill .env file with your db credentials. Check .env.example file for reference.
3. php artisan optimize:clear
4. php artisan optimize
5. php artisan migrate ( If you want to seed the db please use "php artisan migrate --seed")
6. php artisan serve ( To spin up laravel development server )
7. Import the postman collection mirrar_ecom.json and check the endpoints





