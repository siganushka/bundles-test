<?php

declare(strict_types=1);

namespace App\Media;

use Siganushka\MediaBundle\AbstractChannel;
use Symfony\Component\Validator\Constraints\File as AssertFile;

class TestPdf extends AbstractChannel
{
    public function getConstraint(): AssertFile
    {
        $constraint = new AssertFile();
        $constraint->mimeTypes = ['application/pdf', 'application/x-pdf'];

        return $constraint;
    }
}
