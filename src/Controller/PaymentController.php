<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\PaymentOrder;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\GenericBundle\Controller\Crud\Web\IndexTrait;
use Siganushka\PaymentBundle\Entity\AbstractPayment;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Exception\PaymentFailedException;
use Siganushka\PaymentBundle\Form\PaymentRefundType;
use Siganushka\PaymentBundle\Gateway\WxpayJsapi;
use Siganushka\PaymentBundle\PaymentManagerInterface;
use Siganushka\PaymentBundle\Repository\PaymentRefundRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/payments', requirements: ['id' => '[0-9a-zA-Z]+'])]
class PaymentController extends AbstractController
{
    use IndexTrait;

    public function __construct()
    {
        $this->configureCrud(
            entityName: AbstractPayment::class,
            entityIdentifier: 'number',
        );
    }

    #[Route('/{number}/refund')]
    public function refund(Request $request, PaymentManagerInterface $paymentManager, PaymentRefundRepository $paymentRefundRepository, string $number): Response
    {
        /** @var AbstractPayment */
        $entity = $this->findEntity($number);
        if (!$entity->isRefundSupported()) {
            throw new BadRequestHttpException('The payment does not support refund.');
        }

        $refundableAmount = $entity->getRefundableAmount();
        if ($refundableAmount <= 0) {
            throw new BadRequestHttpException('The payment has been fully refunded.');
        }

        $refund = $paymentRefundRepository->createFromPayment($entity);
        $refund->setAmount($refundableAmount);

        $form = $this->createForm(PaymentRefundType::class, $refund);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $paymentManager->refund($entity, $refund);
            } catch (\Throwable $th) {
                $this->addFlash('danger', $th instanceof PaymentFailedException ? $th->getMessage() : 'Refund failed, please try again.');
            }

            return $this->redirectToRoute('app_order_index');
        }

        return $this->render('payment/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/test')]
    public function test(EntityManagerInterface $entityManager, PaymentManagerInterface $paymentManager): Response
    {
        // // Test Payment
        // $order = $entityManager->find(Order::class, 1);

        // $payment = new PaymentOrder(WxpayJsapi::getName(), $order);
        // $entityManager->persist($payment);

        // $result = $paymentManager->pay($payment);

        // dd(__METHOD__, $result);

        // // Test Refund
        // $payment = $entityManager->find(PaymentInterface::class, 1)
        //     ?? throw $this->createNotFoundException();

        // $refund = AbstractPaymentRefund::createFromPayment($payment);
        // $refund->setAmount($payment->getRefundableAmount());

        // $result = $paymentManager->refund($payment, $refund);

        // dd(__METHOD__, $result);

        return new Response(__METHOD__);
    }

    #[Route('/PaymentRefundType')]
    public function PaymentRefundType(Request $request): Response
    {
        $form = $this->createForm(PaymentRefundType::class)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('payment/form.html.twig', [
            'form' => $form,
        ]);
    }
}
