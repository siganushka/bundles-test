<?php

declare(strict_types=1);

namespace App\Media;

use Siganushka\MediaBundle\AbstractChannel;
use Symfony\Component\Validator\Constraints\Image;

class TestImg extends AbstractChannel
{
    public function getConstraint(): Image
    {
        $constraint = new Image();
        $constraint->mimeTypes = ['image/png'];

        return $constraint;
    }
}
