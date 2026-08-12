<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\MediaBundle\MediaManagerInterface;
use Siganushka\MediaBundle\Model\MediaInterface;
use Siganushka\MediaBundle\Rule;
use Siganushka\MediaBundle\Utils\FileUtils;
use Symfony\Contracts\Service\Attribute\Required;

trait AddMediaHandlerTrait
{
    #[Required]
    public EntityManagerInterface $entityManager;

    #[Required]
    public MediaManagerInterface $mediaManager;

    protected function handleMedia(string|Rule $rule, string $url): MediaInterface
    {
        if (str_starts_with($url, '//')) {
            $url = 'https:'.$url;
        }

        $meida = $this->mediaManager->save($rule, FileUtils::createFromUrl($url));

        $this->entityManager->persist($meida);
        $this->entityManager->flush();

        return $meida;
    }
}
