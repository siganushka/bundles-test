<?php

declare(strict_types=1);

namespace App\EventListener;

use Siganushka\ApiFactoryBundle\Event\WechatJscodeAuthenticationFailureEvent;
use Siganushka\ApiFactoryBundle\Event\WechatJscodeAuthenticationSuccessEvent;
use Siganushka\GenericBundle\Response\ProblemJsonResponse;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class WechatJscodeAuthenticationListener
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly TranslatorInterface $translator)
    {
    }

    #[AsEventListener]
    public function onWechatJscodeAuthenticationSuccess(WechatJscodeAuthenticationSuccessEvent $event): void
    {
        $user = $event->getToken()->getUser();
        $data = $this->normalizer->normalize($user, context: [
            AbstractNormalizer::GROUPS => ['user.item'],
        ]);

        $event->setResponse(new JsonResponse($data));
    }

    #[AsEventListener]
    public function onWechatJscodeAuthenticationFailure(WechatJscodeAuthenticationFailureEvent $event): void
    {
        $exception = $event->getException();
        $detail = $this->translator->trans($exception->getMessageKey(), $exception->getMessageData(), 'security');

        $response = new ProblemJsonResponse($detail, ProblemJsonResponse::HTTP_UNAUTHORIZED);
        $response->headers->set('WWW-Authenticate', 'Bearer');

        $event->setResponse($response);
    }
}
