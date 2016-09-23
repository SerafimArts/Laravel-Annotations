# Laravel-Annotations

This is a Doctrine Annotations bridge for Laravel 5+

## Installation

`composer require serafim/laravel-annotations`

In `app.php` add:
```
'providers' => [
    ...
    \Serafim\Annotations\LaravelServiceProvider::class,
]
```

Run `php artisan vendor:publish` if you want configure default behaviuor

## Usage

```
use octrine\Common\Annotations\Reader;

app(Reader::class)->getClassAnnotations($reflection); 
// or app('annotations')->..
```

> NOTE: For more information about annotations read: http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/annotations.html
