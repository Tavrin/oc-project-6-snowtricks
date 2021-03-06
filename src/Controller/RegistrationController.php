<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Manager\UserManager;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\String\Slugger\SluggerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private UserManager $userManager;

    public function __construct(EmailVerifier $emailVerifier, UserManager $userManager)
    {
        $this->emailVerifier = $emailVerifier;
        $this->userManager = $userManager;

    }

    /**
     * @Route("/register", name="app_register", methods={"POST", "GET"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setCreatedat(new DateTime);
            $user->setSlug($slugger->slug($user->getUsername()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->userManager->sendVerificationEmail($this->emailVerifier, $user);
            $this->addFlash(
                'success',
                'Bienvenue, vous pouvez maintenant commenter les figures mais vous devez encore confirmer votre email avant de pouvoir en cr??er, modifier ou supprimer'
            );

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main'
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/send", name="app_send_verify", methods={"POST", "GET"})
     */
    public function sendVerificationEmail(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $this->userManager->sendVerificationEmail($this->emailVerifier, $user);

        $this->addFlash(
            'success',
            'Une demande de confirmation a bien ??t?? renvoy??e ?? '.$user->getEmail()
        );

        return $this->redirectToRoute('user');
    }

    /**
     * @Route("/verify/email", name="app_verify_email", methods={"POST", "GET"})
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre adresse email a bien ??t?? confirm??e');

        return $this->redirectToRoute('index');
    }
}
