<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Siganushka\ApiFactory\Wxpay\NotifyHandler;
use Siganushka\GenericBundle\Controller\Crud\Web\IndexTrait;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Enum\PaymentState;
use Siganushka\PaymentBundle\Event\PaymentSuccessEvent;
use Siganushka\PaymentBundle\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/payments')]
class PaymentController extends AbstractController
{
    use IndexTrait;

    public function __construct(private readonly PaymentRepository $paymentRepository)
    {
        $this->configureCrud(
            entityName: Payment::class,
        );
    }

    #[Route('/wxpay-notify')]
    public function wxpayNotify(Request $request, EventDispatcherInterface $dispatcher, NotifyHandler $handler): Response
    {
        try {
            $data = $handler->handle($request);
        } catch (\Throwable $th) {
            return $handler->fail($th->getMessage());
        }

        $entity = $this->paymentRepository->findOneByNumber($data['out_trade_no']);
        if (!$entity) {
            return $handler->fail('Payment not found.');
        }

        if ($entity->getAmount() !== $data['total_fee']) {
            return $handler->fail('The total_fee is not valid.');
        }

        $this->entityManager->beginTransaction();

        $entity->setDetails($data);
        $entity->setState(PaymentState::Succeed);
        $dispatcher->dispatch(new PaymentSuccessEvent($entity));

        $this->entityManager->flush();
        $this->entityManager->commit();

        return $handler->success();
    }

    #[Route('/alipay-notify')]
    public function alipayNotify(Request $request, LoggerInterface $logger): Response
    {
        $payload = $request->getPayload()->all();
        $logger->info('Alipay notify payload.', $payload);

        return $this->json($payload);
    }
}
