<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\MediaBundle\Entity\Media as BaseMedia;

#[ORM\Entity]
class Media extends BaseMedia
{
}
