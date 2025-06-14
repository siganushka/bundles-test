<?php

declare(strict_types=1);

namespace App\Controller;

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
    public function __construct(protected readonly RegionRepository $repository)
    {
    }

    #[Route('/regions/RegionType')]
    public function RegionType(Request $request): Response
    {
        // $parent = $this->repository->find('61');

        $builder = $this->createFormBuilder()
            ->add('province', RegionType::class, [
                'label' => 'region.province',
                'cascader_target' => 'city',
                'constraints' => new NotBlank(),
            ])
            ->add('city', RegionType::class, [
                'label' => 'region.city',
                'cascader_target' => 'district',
                'constraints' => new NotBlank(),
            ])
            ->add('district', RegionType::class, [
                'label' => 'region.district',
                'cascader_target' => 'street',
                'constraints' => new NotBlank(),
            ])
            ->add('street', RegionType::class, [
                'label' => 'region.street',
                'constraints' => new NotBlank(),
            ])
            ->add('submit', SubmitType::class, ['label' => 'generic.submit'])
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
