<?php

declare(strict_types=1);

namespace App;

use App\Doctrine\Type\BrickMoneyType;
use Doctrine\DBAL\Types\Type;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        parent::boot();

        if (!Type::hasType('money')) {
            /** @var string */
            $currency = $_ENV['APP_CURRENCY'] ?? 'USD';
            Type::addType('money', new BrickMoneyType($currency));
        }
    }
}
