<?php
/**
 * This file is part of Laravel Annotations package.
 *
 * @author Serafim <nesk@xakep.ru>
 * @date 03.05.2016 19:56
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Serafim\Annotations;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Repository as CacheInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Serafim\Annotations\Storage\DoctrineStorageBridge;

/**
 * Class LaravelServiceProvider
 * @package Serafim\Annotations
 */
class LaravelServiceProvider extends ServiceProvider
{
    const CONTAINER_KEY = 'annotations';

    /**
     * @throws \InvalidArgumentException
     */
    public function register()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        AnnotationRegistry::registerLoader(function ($class) use ($config) {
            $namespaces = $config->get('laravel-annotations.namespaces');

            if ($namespaces === [] || Str::startsWith($class, $namespaces)) {
                return class_exists($class);
            }
        });

        $this->app->singleton(Reader::class, function (Container $app) use ($config) {
            $this->mergeConfigFrom($this->getConfigPath(), 'laravel-annotations');

            if ($config->get('laravel-annotations.cache')) {
                /** @var CacheInterface $cache */
                $cache = $app->make(CacheInterface::class);

                $storage = new DoctrineStorageBridge($cache);

                return new CachedReader(new AnnotationReader, $storage, $config->get('laravel-annotations.debug'));
            }

            return new AnnotationReader;
        });

        $this->app->alias(Reader::class, static::CONTAINER_KEY);
    }

    /**
     * @return string
     */
    private function getConfigPath()
    {
        return  __DIR__ . '/../config/laravel-annotations.php';
    }

    /**
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            // Config
            $this->getConfigPath() => config_path('laravel-annotations.php'),
        ]);
    }
}
