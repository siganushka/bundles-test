<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\UserBundle\Entity\User as BaseUser;

#[ORM\Entity]
class User extends BaseUser
{
}
