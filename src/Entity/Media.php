<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\MediaBundle\Entity\AbstractMedia;

#[ORM\Entity(readOnly: true)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
class Media extends AbstractMedia
{
}
