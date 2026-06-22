<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class EntityDenormalizer implements DenormalizerInterface
{
    public const IDENTIFIER_FIELD = 'identifier_field';

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param int|string                                                        $data
     * @param class-string<object>                                              $type
     * @param array{ identifier_field?: string, deserialization_path?: string } $context
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        /** @var string */
        $field = $context[self::IDENTIFIER_FIELD] ?? $this->entityManager->getClassMetadata($type)->getSingleIdentifierFieldName();
        /** @var string|null */
        $path = $context['deserialization_path'] ?? null;

        return $this->entityManager->getRepository($type)->findOneBy([$field => $data])
            ?? throw NotNormalizableValueException::createForUnexpectedDataType(\sprintf('The %s "%s" for entity "%s" does not exist.', $field, $data, $type), $data, [$type], $path, true);
    }

    /**
     * @param class-string<object> $type
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return !$this->entityManager->getMetadataFactory()->isTransient($type) && (\is_int($data) || \is_string($data));
    }

    public function getSupportedTypes(?string $format): array
    {
        return ['object' => false];
    }
}
