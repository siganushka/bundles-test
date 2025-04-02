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
            new TwigFilter('intl_date', [IntlExtensionRuntime::class, 'formatDate'], ['needs_environment' => true]),
            new TwigFilter('intl_currency', [IntlExtensionRuntime::class, 'formatCurrency']),
        ];
    }
}
