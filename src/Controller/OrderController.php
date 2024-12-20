<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Siganushka\OrderBundle\Event\OrderCreatedEvent;
use Siganushka\OrderBundle\Form\OrderItemType;
use Siganushka\OrderBundle\Form\OrderType;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OrderController extends AbstractController
{
    public function __construct(protected readonly OrderRepository $repository)
    {
    }

    #[Route('/orders')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->repository->createQueryBuilderWithOrdered('m');

        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('size', 10);

        $pagination = $paginator->paginate($queryBuilder, $page, $size);

        return $this->render('order/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/orders/new')]
    public function new(Request $request, EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher): Response
    {
        $entity = $this->repository->createNew();

        $form = $this->createForm(OrderType::class, $entity);
        $form->add('Submit', SubmitType::class, ['label' => 'generic.submit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventDispatcher->dispatch(new OrderBeforeCreateEvent($entity));

            $entityManager->persist($entity);
            $entityManager->flush();

            $eventDispatcher->dispatch(new OrderCreatedEvent($entity));

            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('app_order_index');
        }

        return $this->render('order/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/orders/{number}/edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, string $number): Response
    {
        $entity = $this->repository->findOneByNumber($number);
        if (!$entity) {
            throw $this->createNotFoundException(\sprintf('Resource #%d not found.', $number));
        }

        $form = $this->createForm(OrderType::class, $entity);
        $form->add('Submit', SubmitType::class, ['label' => 'generic.save']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('app_order_index');
        }

        return $this->render('order/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/orders/{number}/workflow/{transition}')]
    public function workflow(Request $request, EntityManagerInterface $entityManager, WorkflowInterface $orderStateFlow, string $number, string $transition): Response
    {
        $entity = $this->repository->findOneByNumber($number);
        if (!$entity) {
            throw $this->createNotFoundException(\sprintf('Resource #%d not found.', $number));
        }

        $entityManager->beginTransaction();

        try {
            $orderStateFlow->apply($entity, $transition);
        } catch (LogicException $e) {
            $entityManager->rollback();

            $this->addFlash('danger', $e->getMessage());

            return $this->tryRedirectToRoute($request, 'app_order_index');
        }

        $entityManager->flush();
        $entityManager->commit();

        $this->addFlash('success', 'Your changes were saved!');

        return $this->tryRedirectToRoute($request, 'app_order_index');
    }

    #[Route('/orders/{number}/show')]
    public function show(string $number): Response
    {
        $entity = $this->repository->findOneByNumber($number);
        if (!$entity) {
            throw $this->createNotFoundException(\sprintf('Resource #%d not found.', $number));
        }

        return $this->render('order/show.html.twig', [
            'entity' => $entity,
        ]);
    }

    #[Route('/orders/{number}/delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, string $number): Response
    {
        $entity = $this->repository->findOneByNumber($number);
        if (!$entity) {
            throw $this->createNotFoundException(\sprintf('Resource #%d not found.', $number));
        }

        $entityManager->remove($entity);
        $entityManager->flush();

        $this->addFlash('success', 'The resource has been deleted successfully!');

        return $this->tryRedirectToRoute($request, 'app_order_index');
    }

    #[Route('/orders/OrderType')]
    public function OrderType(Request $request): Response
    {
        $form = $this->createForm(OrderType::class)
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('order/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/orders/OrderItemType')]
    public function OrderItemType(Request $request): Response
    {
        $form = $this->createForm(OrderItemType::class)
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('order/form.html.twig', [
            'form' => $form,
        ]);
    }

    public function tryRedirectToRoute(Request $request, string $route, array $parameters = [], int $status = 302): Response
    {
        if ($referer = $request->headers->get('referer')) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute($route, $parameters, $status);
    }
}
