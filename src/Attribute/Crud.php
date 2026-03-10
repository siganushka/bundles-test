<?php

declare(strict_types=1);

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Crud
{
    public function __construct(
        public readonly string $entityName,
        public readonly ?string $entityForm = null,
        public readonly ?string $entityIdentifier = null,
        public readonly ?string $entityAlias = null,
        public readonly array $serializationCollectionContext = [],
        public readonly array $serializationItemContext = [],
        public readonly array $operations = [],
    ) {
    }
}
