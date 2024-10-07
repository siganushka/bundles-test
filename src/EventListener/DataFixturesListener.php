<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\KernelInterface;

final class DataFixturesListener
{
    public function __construct(private readonly KernelInterface $kernel)
    {
    }

    #[AsEventListener(event: ConsoleTerminateEvent::class)]
    public function onConsoleTerminateEvent(ConsoleTerminateEvent $event): void
    {
        $commandName = $event->getCommand()?->getName();
        if ('doctrine:fixtures:load' !== $commandName) {
            return;
        }

        $input = new ArrayInput([
            'command' => 'siganushka:region:update',
            '--with-street' => true,
        ]);

        $application = new Application($this->kernel);
        $application->setAutoExit(false);
        $application->run($input);
    }
}
