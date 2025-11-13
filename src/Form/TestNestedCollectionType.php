<?php

declare(strict_types=1);

namespace App\Form;

use Siganushka\ProductBundle\Form\Type\ProductOptionValuesCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TestNestedCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    }

    public function getParent(): string
    {
        return ProductOptionValuesCollectionType::class;
    }
}
