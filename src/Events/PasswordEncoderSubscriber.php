<?php

namespace App\Events;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordEncoderSubscriber implements EventSubscriberInterface {

    /** 
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['encodePassword', EventPriorities::PRE_WRITE]
        ];
    }

    public function encodePassword(ViewEvent $event) {
        $user = $event->getControllerResult(); // Password reçu après la désérialisation
        $method = $event->getRequest()->getMethod(); // POST, GET, PUT, ...

        if ($user instanceof User && Request::METHOD_POST === $method) {
            $hash = $this->encoder->encodePassword($user, $user->getPassword()); // Encode le password
            $user->setPassword($hash); // Set le password encodé en BDD
        }
    }
}