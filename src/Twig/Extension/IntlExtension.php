<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Twig\Runtime\IntlExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class IntlExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('datetime', [IntlExtensionRuntime::class, 'formatDatetime'], ['needs_environment' => true]),
            new TwigFilter('currency', [IntlExtensionRuntime::class, 'formatCurrency']),
        ];
    }
}
