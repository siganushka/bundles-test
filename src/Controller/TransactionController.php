<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Topup;
use App\Entity\TransactionOrder;
use App\Entity\TransactionOrderAggregate;
use App\Entity\TransactionTopup;
use App\Payment\WxpayJsapi;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Siganushka\ApiFactory\Wxpay\NotifyHandler;
use Siganushka\GenericBundle\Controller\Crud\Web\IndexTrait;
use Siganushka\TransactionBundle\Entity\Transaction;
use Siganushka\TransactionBundle\Event\TransactionSuccessEvent;
use Siganushka\TransactionBundle\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/transactions')]
class TransactionController extends AbstractController
{
    use IndexTrait;

    public function __construct(private readonly TransactionRepository $transactionRepository)
    {
        $this->configureCrud(
            entityName: Transaction::class,
        );
    }

    #[Route('/test')]
    public function test(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher): Response
    {
        // $order1 = $entityManager->find(Order::class, 1);
        // $order2 = $entityManager->find(Order::class, 2);

        // $topup = $entityManager->find(Topup::class, 1);

        // dd(__METHOD__,
        //     $order1->getTransactions()->toArray(),
        //     $order1->getAggregateTransactions()->toArray(),
        //     $order2->getAggregateTransactions()->toArray(),
        //     $topup->getTransactions()->toArray(),
        // );

        // // 单个订单交易
        // $transaction = new TransactionOrder();
        // $transaction->setOrder($order1);

        // $entityManager->persist($transaction);
        // $entityManager->flush();
        // dd(__METHOD__, $transaction);

        // // 多个订单合并交易
        // $transaction = new TransactionOrderAggregate();
        // $transaction->addOrder($order1);
        // $transaction->addOrder($order2);

        // $entityManager->persist($transaction);
        // $entityManager->flush();
        // dd(__METHOD__, $transaction);

        // // 冲值交易
        // $transaction = new TransactionTopup();
        // $transaction->setTopup($topup);

        // $entityManager->persist($transaction);
        // $entityManager->flush();
        // dd(__METHOD__, $transaction);

        // // 交易成功事件
        // $transaction = $entityManager->find(Transaction::class, 1);
        // $transaction->setPayMethod('foo');
        // $transaction->setPayResponse(['msg' => 'hello']);
        // $transaction->setSuccessful(true);

        // $event = new TransactionSuccessEvent($transaction);
        // $eventDispatcher->dispatch($event);

        // $entityManager->persist($transaction);
        // $entityManager->flush();
        // dd(__METHOD__, $transaction);

        // 查询所有交易
        $entities = $this->transactionRepository->findBy([], ['id' => 'DESC']);
        // dd(__METHOD__, $entities);

        return $this->json($entities, context: [
            AbstractNormalizer::GROUPS => ['transaction.collection'],
        ]);
    }

    #[Route('/wxpay-notify')]
    public function wxpayNotify(Request $request, EventDispatcherInterface $dispatcher, NotifyHandler $handler): Response
    {
        try {
            $data = $handler->handle($request);
        } catch (\Throwable $th) {
            return $handler->fail($th->getMessage());
        }

        $transaction = $this->transactionRepository->findOneByNumber($data['out_trade_no']);
        if (!$transaction) {
            return $handler->fail('Transaction not found.');
        }

        if ($transaction->getAmount() !== $data['total_fee']) {
            return $handler->fail('The total_fee is not valid.');
        }

        $this->entityManager->beginTransaction();

        $transaction->setGateway(WxpayJsapi::getName());
        $transaction->setDetails($data);

        $event = new TransactionSuccessEvent($transaction);
        $dispatcher->dispatch($event);

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
