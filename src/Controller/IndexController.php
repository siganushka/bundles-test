<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\ProductVariant;
use App\Form\TestType;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class IndexController extends AbstractController
{
    #[Route('/')]
    public function index(
        EntityManagerInterface $entityManager,
        OrderNumberGeneratorInterface $numberGenerator): Response
    {
        // $subject = $entityManager->find(ProductVariant::class, 109);

        // $entityManager->beginTransaction();

        // $entity = new Order;
        // $entity->setNumber($numberGenerator->generate($entity));
        // $entity->addItem(new OrderItem($subject, 2));

        // $entityManager->persist($entity);
        // $entityManager->flush();

        // $entityManager->commit();

        return $this->render('index/index.html.twig');
    }

    #[Route('/collection')]
    public function collection(Request $request): Response
    {
        $data = [
            'tags' => ['', '', 'baz'],
            'tests' => [
                ['foo' => 'aaa'],
                ['foo' => 'bbb', 'bar' => 16],
            ],
        ];

        $builder = $this->createFormBuilder($data)
            ->add('tags', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => [
                    'label' => false,
                    'constraints' => new NotBlank(),
                    'row_attr' => ['class' => 'bbb'],
                ],
                'add_button_options' => [
                    'row_attr' => ['class' => 'ccc'],
                ],
                'delete_button_options' => [
                    'row_attr' => ['class' => 'ddd'],
                ],
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('tests', CollectionType::class, [
                'entry_type' => TestType::class,
                'entry_options' => [
                    'row_attr' => ['class' => 'bbb'],
                ],
                'add_button_options' => [
                    'row_attr' => ['class' => 'ccc'],
                ],
                'delete_button_options' => [
                    'row_attr' => ['class' => 'ddd'],
                ],
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('submit', SubmitType::class)
        ;

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('index/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/intl')]
    public function intl(Request $request): Response
    {
        $builder = $this->createFormBuilder()
            ->add('country', CountryType::class)
            ->add('currency', CurrencyType::class)
            ->add('language', LanguageType::class)
            ->add('locale', LocaleType::class)
            ->add('timezone', TimezoneType::class)
            ->add('submit', SubmitType::class)
        ;

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('index/index.html.twig', [
            'form' => $form,
        ]);
    }
}
