<?php

declare(strict_types=1);

namespace App\Twig\Runtime;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Intl\Currencies;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\Extra\Intl\IntlExtension;

class IntlExtensionRuntime implements RuntimeExtensionInterface
{
    public const CURRENCY_DIVISOR = 100;

    public function __construct(
        #[Autowire(service: 'twig.extension.intl')]
        private readonly IntlExtension $extension,
        #[Autowire(env: 'APP_CURRENCY')]
        private readonly string $currencyCode = 'zh_CN')
    {
    }

    public function formatDate(Environment $env, \DateTimeInterface|string|null $date): string
    {
        return $this->extension->formatDateTime($env, $date, timeFormat: 'short');
    }

    public function formatCurrency(?int $amount): string
    {
        // null to 0
        if (null === $amount) {
            $amount = 0;
        }

        $fractionDigits = Currencies::getFractionDigits($this->currencyCode);
        $divisor = match ($fractionDigits) {
            3 => 1000,
            2 => 100,
            default => 1,
        };

        if (1 !== $divisor) {
            $amount /= $divisor;
        }

        return $this->extension->formatCurrency($amount, $this->currencyCode);
    }
}
