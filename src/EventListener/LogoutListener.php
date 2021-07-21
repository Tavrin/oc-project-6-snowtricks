<?php


namespace App\EventListener;


use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{
    private SessionInterface $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function onSymfonyComponentSecurityHttpEventLogoutEvent(LogoutEvent $event)
    {
        $this->session->getFlashBag()->add('success', 'Déconnecté');
    }
}