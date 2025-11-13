<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\GenericBundle\Dto\PageQueryDto;
use Siganushka\MediaBundle\Form\MediaUploadType;
use Siganushka\MediaBundle\Form\Type\MediaType;
use Siganushka\MediaBundle\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class MediaController extends AbstractController
{
    public function __construct(protected readonly MediaRepository $repository)
    {
    }

    #[Route('/media')]
    public function index(PaginatorInterface $paginator, #[MapQueryString] PageQueryDto $dto): Response
    {
        $queryBuilder = $this->repository->createQueryBuilderWithOrderBy('m');
        $pagination = $paginator->paginate($queryBuilder, $dto->page, $dto->size);

        return $this->render('media/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/media/{hash}/delete')]
    public function delete(EntityManagerInterface $entityManager, string $hash): Response
    {
        $entity = $this->repository->findOneByHash($hash)
            ?? throw $this->createNotFoundException();

        $entityManager->remove($entity);
        $entityManager->flush();

        $this->addFlash('success', 'The resource has been deleted successfully!');

        return $this->redirectToRoute('app_media_index');
    }

    #[Route('/media/MediaUploadType')]
    public function MediaUploadType(Request $request): Response
    {
        $form = $this->createForm(MediaUploadType::class)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('media/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/media/MediaType')]
    public function MediaType(Request $request): Response
    {
        $media = $this->repository->findAll();
        $data = [
            'media1' => $media[0] ?? null,
        ];

        $builder = $this->createFormBuilder($data)
            ->add('media0', MediaType::class, [
                'label' => '默认状态',
                'rule' => 'test_img',
            ])
            ->add('media1', MediaType::class, [
                'label' => '有值状态',
                'rule' => 'test_img',
            ])
            ->add('media2', MediaType::class, [
                'label' => '错误状态',
                'rule' => 'test_img',
            ])
            ->add('media3', MediaType::class, [
                'label' => '禁用状态',
                'rule' => 'test_img',
                'disabled' => true,
            ])
            ->add('media4', MediaType::class, [
                'label' => '自定义尺寸',
                'rule' => 'test_img',
                'style' => 'width: 100%; height: 240px',
            ])
            ->add('media5', MediaType::class, [
                'label' => '自定义文件类型',
                'rule' => 'test_pdf',
            ])
            ->add('media6', MediaType::class, [
                'label' => '自定义表单验证',
                'rule' => 'test_pdf',
                'constraints' => new NotBlank(),
            ])
            ->add('submit', SubmitType::class)
        ;

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form['media2'] instanceof FormInterface) {
            $form['media2']->addError(new FormError('This value should not be blank.'));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('media/form.html.twig', [
            'form' => $form,
        ]);
    }
}
