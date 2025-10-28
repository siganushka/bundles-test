<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('foo', TextType::class, [
                'constraints' => new NotBlank(),
                'row_attr' => ['class' => 'foo_class'],
            ])
            ->add('bar', TextType::class, [
                'constraints' => new NotBlank(),
                'row_attr' => ['class' => 'bar_class'],
            ])
            ->add('baz', CollectionType::class, [
                'row_attr' => ['class' => 'py-0'],
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
