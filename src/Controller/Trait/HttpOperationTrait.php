<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Symfony\Component\Form\FormTypeInterface;

trait HttpOperationTrait
{
    protected function getEntityAlias(): string
    {
        $ref = new \ReflectionClass($this->getEntityFqcn());
        /** @var string */
        $class = preg_replace('/([a-z])([A-Z])/', '$1_$2', $ref->getShortName());

        return strtolower($class);
    }

    public function getIdentifierName(): string
    {
        return 'id';
    }

    /**
     * @return class-string<object>
     */
    abstract protected function getEntityFqcn(): string;

    /**
     * @return class-string<FormTypeInterface>
     */
    abstract protected function getFormType(): string;
}
