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
            ->add('option', TextType::class, [
                'label' => 'Option',
                'constraints' => new NotBlank(),
                'row_attr' => ['class' => 'foo_class'],
            ])
            ->add('values', CollectionType::class, [
                'entry_options' => [
                    'label' => 'Value...',
                ],
                'row_attr' => ['class' => 'py-0'],
                'allow_add' => true,
                'allow_delete' => true,
                'add_button_options' => [
                    'label' => '<i class="bi bi-plus-lg"></i>',
                    'label_html' => true,
                ],
                'delete_button_options' => [
                    'label' => '<i class="bi bi-trash"></i>',
                    'label_html' => true,
                ],
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
