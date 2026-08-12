<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\UserBundle\Entity\AbstractUserLogin;

#[ORM\Entity]
class UserLogin extends AbstractUserLogin
{
}
