<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;
use Money\Currency;
use Money\Money;

class PhpMoney extends Type
{
    /**
     * @param non-empty-string $currency
     */
    public function __construct(private readonly string $currency)
    {
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof Money) {
            return $value->getAmount();
        }

        throw InvalidType::new($value, Money::class, ['null', 'int', Money::class]);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (null === $value || $value instanceof Money) {
            return $value;
        }

        if (\is_int($value) || (\is_string($value) && is_numeric($value))) {
            return new Money($value, new Currency($this->currency));
        }

        throw ValueNotConvertible::new($value, Money::class);
    }
}
