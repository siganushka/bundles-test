<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoneyTypeExtension extends AbstractTypeExtension
{
    public function __construct(
        #[Autowire(env: 'APP_CURRENCY')]
        private readonly string $currencyCode = 'zh_CN')
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $scale = fn (Options $options) => Currencies::getFractionDigits($options['currency']);

        $divisor = function (Options $options) {
            return match ($options['scale']) {
                3 => 1000,
                2 => 100,
                default => 1,
            };
        };

        $resolver->setDefaults([
            'scale' => $scale,
            'divisor' => $divisor,
            'currency' => $this->currencyCode,
            // @see https://symfony.com/doc/current/reference/forms/types/money.html#input
            'input' => 'integer',
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            MoneyType::class,
        ];
    }
}
