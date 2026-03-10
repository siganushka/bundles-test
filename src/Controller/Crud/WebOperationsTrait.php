<?php

declare(strict_types=1);

namespace App\Controller\Crud;

trait WebOperationsTrait
{
    use OperationsTrait;

    protected function getControllerAlias(): string
    {
        return str_replace(['_controller', '_'], '', self::generateAlias($this));
    }

    protected function getTemplateAlias(): string
    {
        return str_replace('_controller', '', self::generateAlias($this));
    }
}
