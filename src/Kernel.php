<?php

declare(strict_types=1);

namespace App;

use App\Doctrine\Type\BrickMoney;
use App\Doctrine\Type\PhpMoney;
use Doctrine\DBAL\Types\Type;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        parent::boot();

        if (!Type::hasType('brick_money')) {
            /** @var string */
            $currency = $_ENV['APP_CURRENCY'] ?? 'USD';
            Type::addType('brick_money', new BrickMoney($currency));
        }

        if (!Type::hasType('php_money')) {
            /** @var non-empty-string */
            $currency = $_ENV['APP_CURRENCY'] ?? 'USD';
            Type::addType('php_money', new PhpMoney($currency));
        }
    }
}
