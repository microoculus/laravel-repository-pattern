# laravel-repository-pattern

"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/microoculus/laravel-repository-pattern"
        }
    ],
    
 "microoculus/laravel-repository-pattern": "^1.0",
composer require microoculus/laravel-repository-pattern
composer update microoculus/laravel-repository-pattern
php artisan vendor:publish --provider="Microoculus\LaravelRepositoryPattern\Providers\RepositoryPatterServiceProvider" --force

Usage
php artisan make:repo Users
php artisan make:repo Users --module="Blog"
php artisan make:repo Users --path="Customer" 
php artisan make:repo Users --path="Customer" --module="Blog"
