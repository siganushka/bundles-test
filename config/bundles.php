<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
    Siganushka\GenericBundle\SiganushkaGenericBundle::class => ['all' => true],
    Knp\Bundle\PaginatorBundle\KnpPaginatorBundle::class => ['all' => true],
    Siganushka\MediaBundle\SiganushkaMediaBundle::class => ['all' => true],
    Siganushka\ProductBundle\SiganushkaProductBundle::class => ['all' => true],
    Siganushka\OrderBundle\SiganushkaOrderBundle::class => ['all' => true],
    Siganushka\RegionBundle\SiganushkaRegionBundle::class => ['all' => true],
    Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class => ['dev' => true, 'test' => true],
    Siganushka\RequestTokenBundle\SiganushkaRequestTokenBundle::class => ['all' => true],
    Symfony\UX\StimulusBundle\StimulusBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\UX\Autocomplete\AutocompleteBundle::class => ['all' => true],
];
