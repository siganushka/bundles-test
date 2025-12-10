<?php

declare(strict_types=1);

namespace App\EventListener;

use Siganushka\AdminBundle\Event\NavbarMenuEvent;
use Siganushka\AdminBundle\Event\SidebarMenuEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class ConfigureMenuListener
{
    #[AsEventListener]
    public function onNavbarMenu(NavbarMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $menu->addChild('Help')
            ->setExtra('icon', 'question-circle-fill')
            ->setExtra('show_label', false)
        ;

        $menu->addChild('Notification', ['route' => 'app_index_index'])
            ->setExtra('icon', 'bell-fill')
            ->setExtra('show_label', false)
        ;
    }

    #[AsEventListener(priority: -20)]
    public function onNavbarUserMenu(NavbarMenuEvent $event): void
    {
        $menu = $event->getMenu()->getChild('siganushka_admin.navbar.user');
        if (!$menu) {
            return;
        }

        $menu->setExtra('img', 'https://github.com/mdo.png');
        $menu->addChild('New project', ['uri' => 'https://cn.bing.com'])->setExtra('icon', 'folder-plus');
        $menu->addChild('Settings', ['uri' => '#'])->setExtra('icon', 'gear');
        $menu->addChild('Profiles')->setExtra('icon', 'person-lines-fill');

        $menu->addChild('siganushka_admin.navbar.user.logout', ['route' => 'app_index_index'])
            ->setLinkAttribute('class', 'text-danger')
            ->setExtra('icon', 'box-arrow-right')
        ;
    }

    #[AsEventListener]
    public function onSidebarMenu(SidebarMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $statistic = $menu->addChild('Statistic', ['route' => 'app_index_index'])->setExtra('icon', 'graph-up-arrow');
        $statistic->addChild('Analysis');
        $statistic->addChild('Monitor');
        $statistic->addChild('Workplace');

        $form = $menu->addChild('Form')->setExtra('icon', 'pencil-square');
        $form->addChild('Basic Form');
        $form->addChild('Step Form');
        $form->addChild('Advanced Form');

        $list = $menu->addChild('List')->setExtra('icon', 'grid');
        $list->addChild('Search List')
            ->addChild('Search List Article')->getParent()
            ?->addChild('Search List Project')->getParent()
            ?->addChild('Search List Application')->getParent()
        ;
        $list->addChild('Search table');
        $list->addChild('Basic List');
        $list->addChild('Card List');

        $profile = $menu->addChild('Profile')->setExtra('icon', 'card-list');
        $profile->addChild('Basic Profile');
        $profile->addChild('Advanced Profile');

        $result = $menu->addChild('Result')->setExtra('icon', 'check-circle');
        $result->addChild('Success');
        $result->addChild('Fail');

        $exception = $menu->addChild('Exception')->setExtra('icon', 'exclamation-circle');
        $exception->addChild('403');
        $exception->addChild('404');
        $exception->addChild('500');

        $result = $menu->addChild('Account')->setExtra('icon', 'person-circle');
        $result->addChild('Account Center');
        $result->addChild('Account Settings');

        $menu->addChild('Abount Me')->setExtra('icon', 'question-circle');
        $menu->addChild('Link to Bing', ['uri' => 'https://cn.bing.com'])->setExtra('icon', 'bing')->setLinkAttribute('target', '_blank');
        $menu->addChild('Test truncate the text with an ellipsis')->setExtra('icon', 'scissors');
    }
}
