<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\GenericBundle\Controller\Crud\Web\DeleteTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\EditTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\IndexTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\NewTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\ShowTrait;
use Siganushka\OrderBundle\Dto\OrderQueryDto;
use Siganushka\OrderBundle\Form\OrderItemType;
use Siganushka\OrderBundle\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/orders', requirements: ['_id' => '[0-9a-zA-Z]+'])]
class OrderController extends AbstractController
{
    use IndexTrait;
    use NewTrait;
    use ShowTrait;
    use EditTrait;
    use DeleteTrait;

    public function __construct(protected readonly OrderRepository $repository)
    {
        $this->configureCrud(
            entityName: Order::class,
            entityForm: OrderType::class,
            entityIdentifier: 'number',
            transactionUsed: true,
        );
    }

    #[Route]
    public function index(PaginatorInterface $paginator, #[MapQueryString] OrderQueryDto $dto): Response
    {
        $queryBuilder = $this->repository->createQueryBuilderByDto('o', $dto);
        $countForState = $this->repository->countByStateMapping();

        $pagination = $paginator->paginate($queryBuilder);

        return $this->render('order/index.html.twig', compact('pagination', 'dto', 'countForState'));
    }

    #[Route('/{number}/workflow/{transition}')]
    public function workflow(EntityManagerInterface $entityManager, WorkflowInterface $orderStateMachine, string $number, string $transition): Response
    {
        $entity = $this->repository->findOneByNumber($number)
            ?? throw $this->createNotFoundException();

        $entityManager->beginTransaction();

        try {
            $orderStateMachine->apply($entity, $transition);
        } catch (LogicException $e) {
            $entityManager->rollback();

            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('app_order_index');
        }

        $entityManager->flush();
        $entityManager->commit();

        $this->addFlash('success', 'Your changes were saved!');

        return $this->redirectToRoute('app_order_index');
    }

    #[Route('/OrderType')]
    public function OrderType(Request $request): Response
    {
        $form = $this->createForm(OrderType::class)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('order/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/OrderItemType')]
    public function OrderItemType(Request $request): Response
    {
        $form = $this->createForm(OrderItemType::class)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('order/form.html.twig', [
            'form' => $form,
        ]);
    }
}
