<?php

declare(strict_types=1);

namespace App\Form\Extension;

use App\Entity\Product;
use Siganushka\ProductBundle\Form\ProductType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('virtual', CheckboxType::class, [
                'label' => 'product.virtual',
            ])
            ->add('weight', IntegerType::class, [
                'label' => 'product.weight',
                'constraints' => new NotBlank(groups: ['notVirtualRequired']),
            ])
            ->add('length', IntegerType::class, [
                'label' => 'product.length',
                'constraints' => new NotBlank(groups: ['notVirtualRequired']),
            ])
            ->add('width', IntegerType::class, [
                'label' => 'product.width',
                'constraints' => new NotBlank(groups: ['notVirtualRequired']),
            ])
            ->add('height', IntegerType::class, [
                'label' => 'product.height',
                'constraints' => new NotBlank(groups: ['notVirtualRequired']),
            ])
        ;

        $builder->addEventListener(FormEvents::SUBMIT, $this->clearVirtualField(...));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('validation_groups', function (FormInterface $form) {
            $data = $form->getData();

            return $data instanceof Product && $data->isVirtual()
                ? ['Default']
                : ['Default', 'notVirtualRequired'];
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            ProductType::class,
        ];
    }

    public function clearVirtualField(FormEvent $event): void
    {
        $data = $event->getData();
        if ($data instanceof Product && $data->isVirtual()) {
            $data->setWeight(null);
            $data->setLength(null);
            $data->setWidth(null);
            $data->setHeight(null);
        }
    }
}
