<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use Brick\Math\BigNumber;
use Brick\Money\Money;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;

class BrickMoney extends Type
{
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
            return $value->getMinorAmount()->toInt();
        }

        throw InvalidType::new($value, Money::class, ['null', 'int', Money::class]);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (null === $value || $value instanceof Money) {
            return $value;
        }

        if ($value instanceof BigNumber || \is_float($value) || \is_int($value) || \is_string($value)) {
            return Money::ofMinor($value, 'KWD');
        }

        throw ValueNotConvertible::new($value, Money::class);
    }
}
