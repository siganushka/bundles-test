<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\OrderBundle\Entity\OrderAdjustment;
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

#[Route('/orders')]
class OrderController extends AbstractController
{
    public function __construct(protected readonly OrderRepository $orderRepository)
    {
    }

    #[Route]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->orderRepository->createQueryBuilder('m');

        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('size', 10);

        $pagination = $paginator->paginate($queryBuilder, $page, $size);

        return $this->render('order/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new')]
    public function new(Request $request, EventDispatcherInterface $eventDispatcher, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->orderRepository->createNew();

        $form = $this->createForm(OrderType::class, $entity);
        $form->add('Submit', SubmitType::class, ['label' => 'generic.submit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adjustment1 = new OrderAdjustment();
            $adjustment1->setAmount(100);

            $adjustment2 = new OrderAdjustment();
            $adjustment2->setAmount(-200);

            $entity->addAdjustment($adjustment1);
            $entity->addAdjustment($adjustment2);

            $event = new OrderBeforeCreateEvent($entity);
            $eventDispatcher->dispatch($event);

            $entityManager->persist($entity);
            $entityManager->flush();

            $event = new OrderCreatedEvent($entity);
            $eventDispatcher->dispatch($event);

            $this->addFlash('success', sprintf('Resource #%s has been created!', $entity->getNumber()));

            return $this->redirectToRoute('app_order_index');
        }

        return $this->render('order/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{number<\d{16}>}/edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, string $number): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%s not found.', $number));
        }

        $form = $this->createForm(OrderType::class, $entity);
        $form->add('Submit', SubmitType::class, ['label' => 'generic.submit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', sprintf('Resource #%s has been updated!', $number));

            return $this->redirectToRoute('app_order_index');
        }

        return $this->render('order/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{number<\d{16}>}/workflow/{transition}')]
    public function workflow(EntityManagerInterface $entityManager, WorkflowInterface $orderState, string $number, string $transition): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%s not found.', $number));
        }

        try {
            $orderState->apply($entity, $transition);
        } catch (LogicException $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('app_order_index');
        }

        $entityManager->flush();
        $this->addFlash('success', sprintf('Resource #%s has been updated!', $number));

        return $this->redirectToRoute('app_order_index');
    }

    #[Route('/{number<\d{16}>}/show')]
    public function show(Request $request, string $number): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%s not found.', $number));
        }

        return $this->render('order/show.html.twig', [
            'entity' => $entity,
        ]);
    }

    #[Route('/{number<\d{16}>}/delete')]
    public function delete(EntityManagerInterface $entityManager, string $number): Response
    {
        $entity = $this->orderRepository->findOneByNumber($number);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%s not found.', $number));
        }

        $entityManager->remove($entity);
        $entityManager->flush();

        $this->addFlash('success', sprintf('Resource #%s has been deleted!', $number));

        return $this->redirectToRoute('app_order_index');
    }

    #[Route('/OrderType')]
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

    #[Route('/OrderItemType')]
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
}
