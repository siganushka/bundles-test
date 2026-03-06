<?php

declare(strict_types=1);

namespace App\Routing;

use App\Attribute\Crud;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

#[AutoconfigureTag('routing.loader')]
class CrudRouteLoader extends Loader
{
    private bool $loaded = false;

    public function __construct(#[AutowireIterator('app.crud')] private readonly iterable $controllers)
    {
    }

    public function load(mixed $resource, ?string $type = null): mixed
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "crud" loader twice');
        }

        $routes = new RouteCollection();

        foreach ($this->controllers as $controller) {
            $ref = new \ReflectionClass($controller);
            if (!$attribute = ($ref->getAttributes(Crud::class)[0] ?? null)) {
                continue;
            }

            /** @var string */
            $controllerName = preg_replace('/([a-z])([A-Z])/', '$1_$2', $ref->getShortName());
            $controllerAlias = str_replace(['_controller', '_'], '', strtolower($controllerName));

            /** @var Crud */
            $crud = $attribute->newInstance();
            // dd($controllerName, $controllerAlias, $crud);

            $routes->add(\sprintf('%s_get_collection', $controllerAlias), new Route(
                path: \sprintf('/%s', $controllerAlias),
                methods: 'GET',
            ));

            $routes->add(\sprintf('%s_post_collection', $controllerAlias), new Route(
                path: \sprintf('/%s', $controllerAlias),
                methods: 'POST',
            ));

            $routes->add(\sprintf('%s_get_item', $controllerAlias), new Route(
                path: \sprintf('/%s/{%s}', $controllerAlias, $crud->entityIdentifier ?? 'id'),
                methods: 'GET',
            ));

            $routes->add(\sprintf('%s_put_item', $controllerAlias), new Route(
                path: \sprintf('/%s/{%s}', $controllerAlias, $crud->entityIdentifier ?? 'id'),
                methods: ['PUT', 'PATCH'],
            ));

            $routes->add(\sprintf('%s_delete_item', $controllerAlias), new Route(
                path: \sprintf('/%s/{%s}', $controllerAlias, $crud->entityIdentifier ?? 'id'),
                methods: 'DELETE',
            ));
        }

        $this->loaded = true;

        return $routes;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return 'crud' === $type;
    }
}
