<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ProductVariant;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Repository\OrderItemRepository;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:order:create-performance-test',
    description: 'Add a short description for your command',
)]
class OrderCreatePerformanceTestCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orderRepository,
        private readonly OrderItemRepository $orderItemRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('count', InputArgument::OPTIONAL, 'Generate count', '100')
            ->addArgument('subjectId', InputArgument::OPTIONAL, 'Generated subject id', '109')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $count = $input->getArgument('count');
        $subjectId = $input->getArgument('subjectId');

        if (!is_numeric($count)) {
            throw new \InvalidArgumentException('The argument count must be numeric.');
        }

        if (!is_numeric($subjectId)) {
            throw new \InvalidArgumentException('The argument subjectId must be numeric.');
        }

        $subject = $this->entityManager->find(ProductVariant::class, $subjectId);

        $preTime = microtime(true);
        for ($i = 0; $i < $count; ++$i) {
            $order = $this->orderRepository->createNew();
            $order->addItem($this->orderItemRepository->createNew($subject, 1));

            $this->entityManager->persist($order);
        }

        $this->entityManager->flush();

        $postTime = microtime(true);
        $execTime = $postTime - $preTime;

        $io->success(\sprintf('共计生成 %d 条记录，耗时 %f'.\PHP_EOL, $count, $execTime));

        return Command::SUCCESS;
    }
}
