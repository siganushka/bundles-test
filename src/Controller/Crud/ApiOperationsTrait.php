<?php

declare(strict_types=1);

namespace App\Controller\Crud;

use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

trait ApiOperationsTrait
{
    use OperationsTrait;

    protected function getSerializationCollectionContext(): array
    {
        return [AbstractNormalizer::GROUPS => \sprintf('%s:collection', self::generateAlias($this->entityName))];
    }

    protected function getSerializationItemContext(): array
    {
        return [AbstractNormalizer::GROUPS => \sprintf('%s:item', self::generateAlias($this->entityName))];
    }
}
