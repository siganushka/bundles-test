<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\PaymentOrder;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\GenericBundle\Controller\Crud\Web\IndexTrait;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Entity\PaymentRefund;
use Siganushka\PaymentBundle\Form\PaymentRefundType;
use Siganushka\PaymentBundle\Gateway\WxpayJsapi;
use Siganushka\PaymentBundle\PaymentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/payments')]
class PaymentController extends AbstractController
{
    use IndexTrait;

    public function __construct()
    {
        $this->configureCrud(
            entityName: Payment::class,
        );
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
        // $payment = $entityManager->find(Payment::class, 1)
        //     ?? throw $this->createNotFoundException();

        // $refund = PaymentRefund::createFromPayment($payment);
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
