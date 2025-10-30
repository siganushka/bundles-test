<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Contracts\Translation\TranslatorInterface;

class MoneyTypeExtension extends AbstractTypeExtension
{
    public const INT_MAX_VALUE = 2147483647;

    public function __construct(
        #[Autowire(env: 'APP_LOCALE')]
        private readonly string $localeCode,
        #[Autowire(env: 'APP_CURRENCY')]
        private readonly string $currencyCode,
        private readonly TranslatorInterface $translator,
    ) {
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

        $resolver->setNormalizer('constraints', function (Options $options, $constraints) {
            $transformer = new MoneyToLocalizedStringTransformer(
                $options['scale'],
                $options['grouping'],
                $options['rounding_mode'],
                $options['divisor'],
                $this->localeCode,
                $options['input'],
            );

            $message = $this->translator->trans('This value should be less than or equal to {{ formatted_value }}.', [
                '{{ formatted_value }}' => $transformer->transform(self::INT_MAX_VALUE),
            ]);

            $constraints = \is_object($constraints) ? [$constraints] : (array) $constraints;
            $constraints[] = new LessThanOrEqual(self::INT_MAX_VALUE, message: $message);

            return $constraints;
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            MoneyType::class,
        ];
    }
}
