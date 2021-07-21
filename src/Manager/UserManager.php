<?php


namespace App\Manager;


use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class UserManager
{
    public function sendVerificationEmail(EmailVerifier $emailVerifier, $user)
    {
        $emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('edstrangedreams@gmail.com', 'SnowTricks'))
                ->to($user->getEmail())
                ->subject('Veuillez confirmer votre email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}