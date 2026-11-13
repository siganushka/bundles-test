<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductOption;
use App\Entity\ProductOptionValue;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Siganushka\GenericBundle\Controller\Crud\Web\DeleteTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\EditTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\IndexTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\NewTrait;
use Siganushka\ProductBundle\Dto\ProductQueryDto;
use Siganushka\ProductBundle\Form\ProductOptionType;
use Siganushka\ProductBundle\Form\ProductOptionValueType;
use Siganushka\ProductBundle\Form\ProductType;
use Siganushka\ProductBundle\Form\ProductVariantType;
use Siganushka\ProductBundle\Form\Type\ProductVariantCollectionType;
use Siganushka\ProductBundle\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[Route('/products')]
class ProductController extends AbstractController
{
    use IndexTrait;
    use NewTrait;
    use EditTrait;
    use DeleteTrait;

    public function __construct(
        protected readonly ProductRepository $repository,
        protected readonly RequestStack $requestStack)
    {
        $this->configureCrud(
            entityName: Product::class,
            entityForm: ProductType::class,
            queryDtoClass: ProductQueryDto::class,
        );
    }

    protected function createEntityQueryBuilder(ProductQueryDto $dto): QueryBuilder
    {
        return $this->repository->createQueryBuilderFromDto('p', $dto);
    }

    protected function isGrantedForOperation(string $operation): bool
    {
        return match ($operation) {
            self::OPERATION_CREATE,
            self::OPERATION_UPDATE => true,
            default => false,
        };
    }

    #[Route('/choices', methods: 'POST')]
    public function choices(Request $request, Environment $twig): Response
    {
        $entity = new Product();

        $form = $this->createForm(ProductType::class, $entity, ['validation_groups' => false]);
        $form->submit($request->getPayload()->all());

        $template = $twig->createTemplate('{{ form_widget(form.variants) }}');
        $content = $twig->render($template, [
            'form' => $form->createView(),
        ]);

        return new Response($content);
    }

    #[Route('/{id<\d+>}/variants', methods: ['GET', 'POST'])]
    public function variants(Request $request, Environment $twig, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->repository->find($id)
            ?? throw $this->createNotFoundException();

        $builder = $this->createFormBuilder($entity, ['csrf_protection' => false])
            ->add('variants', ProductVariantCollectionType::class, [
                'label' => false,
            ])
        ;

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
        }

        $template = $twig->createTemplate('{{ form(form) }}');
        $content = $twig->render($template, [
            'form' => $form->createView(),
        ]);

        return new Response($content, $form->isSubmitted() && !$form->isValid() ? 422 : 200);
    }

    #[Route('/ProductType')]
    public function ProductType(Request $request): Response
    {
        $option1 = new ProductOption('Test');
        $option1->addValue(new ProductOptionValue(name: 'aaa'));
        $option1->addValue(new ProductOptionValue(name: 'bbb'));
        $option1->addValue(new ProductOptionValue(name: 'ccc'));

        $entity = new Product();
        $entity->addOption($option1);

        $form = $this->createForm(ProductType::class, $entity)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/ProductOptionType')]
    public function ProductOptionType(Request $request): Response
    {
        $form = $this->createForm(ProductOptionType::class)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/ProductOptionValueType')]
    public function ProductOptionValueType(Request $request): Response
    {
        $form = $this->createForm(ProductOptionValueType::class)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/ProductVariantType')]
    public function ProductVariantType(Request $request): Response
    {
        $form = $this->createForm(ProductVariantType::class)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }
}
