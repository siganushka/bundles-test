<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Siganushka\ProductBundle\Form\Type\ProductVariantCollectionType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\StimulusBundle\Helper\StimulusHelper;

class ProductVariantCollectionTypeExtension extends AbstractTypeExtension
{
    public function __construct(
        private readonly StimulusHelper $stimulusHelper,
        private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $url = $this->urlGenerator->generate('app_product_choices');

        $attributes = $this->stimulusHelper->createStimulusAttributes();
        $attributes->addController('product-variants', compact('url'));
        $attributes->addAction('product-variants', 'optionsChanged', 'product-options:changed@document');

        $view->vars['attr'] += $attributes->toArray();
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            ProductVariantCollectionType::class,
        ];
    }
}
