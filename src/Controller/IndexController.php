<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\ProductVariant;
use App\Form\TestType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Siganushka\OrderBundle\Enum\OrderState;
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
    public function index(): Response
    {
        return $this->render('index/index.html.twig');
    }

    #[Route('/collection')]
    public function collection(Request $request): Response
    {
        $data = [
            'text' => 'CollectionType Demo',
            'tags' => ['foo', 'bar', 'baz'],
            'tests' => [
                ['foo' => 'aaa', 'bar' => 16],
                ['foo' => 'bbb', 'bar' => 32],
                ['foo' => 'ccc', 'bar' => 64],
            ],
        ];

        $builder = $this->createFormBuilder($data)
            ->add('text', TextType::class)
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
                    'label' => '<i class="bi bi-plus-lg"></i>',
                    'label_html' => true,
                    'row_attr' => ['class' => 'ccc'],
                ],
                'delete_button_options' => [
                    'label' => '<i class="bi bi-trash"></i>',
                    'label_html' => true,
                    'row_attr' => ['class' => 'ddd'],
                ],
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('submit', SubmitType::class, ['label' => 'generic.submit'])
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
            ->add('submit', SubmitType::class, ['label' => 'generic.submit'])
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

    #[Route('/test')]
    public function test(EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        $subject = $entityManager->getRepository(ProductVariant::class)->find(109);

        // $logger->debug('CREATE_BEFORE');

        // $entity = new Order();
        // $entity->addItem(new OrderItem($subject, 1));

        // $entityManager->persist($entity);
        // $entityManager->flush();

        // $logger->debug('CREATE_AFTER');

        // $logger->debug('UPDATE_BEFORE');

        // $entity = $entityManager->getRepository(Order::class)->findBy([], ['id' => 'DESC'])[0];
        // $entity->setNote(uniqid());
        // $entity->setStateAsString(OrderState::Cancelled->value);

        // $entityManager->flush();

        // $logger->debug('UPDATE_AFTER');

        // $logger->debug('DELETE_BEFORE');

        // $entity = $entityManager->getRepository(Order::class)->findBy([], ['id' => 'DESC'])[0];

        // $entityManager->remove($entity);
        // $entityManager->flush();

        // $logger->debug('DELETE_AFTER');

        return $this->render('index/index.html.twig');
    }
}
