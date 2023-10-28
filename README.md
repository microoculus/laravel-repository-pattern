# laravel-repository-pattern
 <br />
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/microoculus/laravel-repository-pattern"
        }
    ],
    <br /> 
 "microoculus/laravel-repository-pattern": "^1.0",  <br />
composer require microoculus/laravel-repository-pattern  <br />
composer update microoculus/laravel-repository-pattern  <br />
php artisan vendor:publish --provider="Microoculus\LaravelRepositoryPattern\Providers\RepositoryPatterServiceProvider" --force  <br />

Usage  <br />
php artisan make:repo Users  <br />
php artisan make:repo Users --module="Blog"  <br />
php artisan make:repo Users --path="Customer"   <br />
php artisan make:repo Users --path="Customer" --module="Blog"  <br />
