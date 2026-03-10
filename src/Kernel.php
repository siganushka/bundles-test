<?php

declare(strict_types=1);

namespace App;

use App\Attribute\Crud;
use App\Doctrine\Type\BrickMoneyType;
use Doctrine\DBAL\Types\Type;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function build(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(Crud::class, static function (ChildDefinition $definition): void {
            $definition->addTag('app.crud');
        });
    }

    public function boot(): void
    {
        parent::boot();

        if (!Type::hasType('money') && \is_string($currency = $_ENV['APP_CURRENCY'] ?? 'USD')) {
            Type::addType('money', new BrickMoneyType($currency));
        }
    }
}
