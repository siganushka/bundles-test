<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Siganushka\ProductBundle\Form\Type\ProductOptionCollectionType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\StimulusBundle\Helper\StimulusHelper;

class ProductOptionCollectionTypeExtension extends AbstractTypeExtension
{
    public function __construct(private readonly StimulusHelper $stimulusHelper)
    {
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $attributes = $this->stimulusHelper->createStimulusAttributes();
        $attributes->addController('product-options');
        $attributes->addAction('product-options', 'autocompleteConnect', 'autocomplete:connect');
        $attributes->addAction('product-options', 'added', 'siganushka-generic-collection:added');
        $attributes->addAction('product-options', 'deleted', 'siganushka-generic-collection:deleted');

        $view->vars['attr'] += $attributes->toArray();
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            ProductOptionCollectionType::class,
        ];
    }
}
