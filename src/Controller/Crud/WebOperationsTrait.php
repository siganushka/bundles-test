<?php

declare(strict_types=1);

namespace App\Controller\Crud;

use Siganushka\GenericBundle\Controller\Crud\OperationsTrait;

trait WebOperationsTrait
{
    use OperationsTrait;

    private function getControllerAlias(): string
    {
        return str_replace(['_controller', '_'], '', self::generateAlias($this));
    }

    private function getTemplateAlias(): string
    {
        return str_replace('_controller', '', self::generateAlias($this));
    }
}
