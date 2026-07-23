<?php

declare(strict_types=1);

namespace App\EventListener;

use Siganushka\ApiFactoryBundle\Event\AuthenticationFailureEvent;
use Siganushka\ApiFactoryBundle\Event\AuthenticationSuccessEvent;
use Siganushka\ApiFactoryBundle\Security\Http\Authenticator\WechatJscodeAuthenticator;
use Siganushka\GenericBundle\Response\ProblemJsonResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApiFactoryAuthenticationListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly TranslatorInterface $translator)
    {
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getToken()->getUser();
        $data = $this->normalizer->normalize($user, context: [
            AbstractNormalizer::GROUPS => ['user.item'],
        ]);

        $event->setResponse(new JsonResponse($data));
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $exception = $event->getException();
        $detail = $this->translator->trans($exception->getMessageKey(), $exception->getMessageData(), 'security');

        $response = new ProblemJsonResponse($detail, ProblemJsonResponse::HTTP_UNAUTHORIZED);
        $response->headers->set('WWW-Authenticate', 'Bearer');

        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            AuthenticationSuccessEvent::getAuthenticator(WechatJscodeAuthenticator::class) => 'onAuthenticationSuccess',
            AuthenticationFailureEvent::getAuthenticator(WechatJscodeAuthenticator::class) => 'onAuthenticationFailure',
        ];
    }
}
