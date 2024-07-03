<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\RegionBundle\Form\Type\RegionType;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegionController extends AbstractController
{
    protected RegionRepository $regionRepository;

    public function __construct(RegionRepository $regionRepository)
    {
        $this->regionRepository = $regionRepository;
    }

    #[Route('/RegionType')]
    public function RegionType(Request $request, EntityManagerInterface $entityManager): Response
    {
        // $parent = $this->regionRepository->find('61');

        $builder = $this->createFormBuilder()
            ->add('province', RegionType::class, [
                'label' => 'region.province',
                'placeholder' => 'generic.choice',
                'constraints' => new NotBlank(),
                'cascader_target' => 'city',
            ])
            ->add('city', RegionType::class, [
                'label' => 'region.city',
                'placeholder' => 'generic.choice',
                'constraints' => new NotBlank(),
                'cascader_target' => 'district',
            ])
            ->add('district', RegionType::class, [
                'label' => 'region.district',
                'placeholder' => 'generic.choice',
                'constraints' => new NotBlank(),
                'cascader_target' => 'street',
            ])
            ->add('street', RegionType::class, [
                'label' => 'region.street',
                'placeholder' => 'generic.choice',
                'constraints' => new NotBlank(),
            ])
            ->add('submit', SubmitType::class, ['label' => 'generic.submit', 'priority' => -1])
        ;

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $entityManager->persist($address);
            // $entityManager->flush();

            dd(__METHOD__, $form->getData());
        }

        return $this->render('region/form.html.twig', [
            'form' => $form,
        ]);
    }
}
