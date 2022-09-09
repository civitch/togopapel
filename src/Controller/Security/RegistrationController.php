<?php


namespace App\Controller\Security;


use App\Entity\User;
use App\Form\Register\RegistrationParticularType;
use App\Form\Register\RegistrationProType;
use App\Services\App\AppSecurity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController
 * @package App\Controller\Security
 * @Route("/creation")
 */
class RegistrationController extends AbstractController
{
    const CONFIRM_ACCOUNT_CREATE = 'confirm_message_account_create';
    const PATH_REGISTER_TPL = 'Security/Register/';
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param Request $request
     * @param AppSecurity $appSecurity
     * @return Response|RedirectResponse
     * @Route("/compte/particulier", name="register_account_particular", methods={"GET", "POST"})
     */
    public function registerParticular(Request $request, AppSecurity $appSecurity): Response
    {
        if($this->getUser()){
            return $appSecurity->redirectDashboard();
        }
        $user = new User();
        $indicatifzero = $this->getDoctrine()
            ->getRepository('App\Entity\IndicatifPays')
            ->find(0);
        $user->setIndicatifPays($indicatifzero);
        $form = $this->createForm(RegistrationParticularType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $appSecurity->createAccount($user,'particular');
            return $this->paramsRedirectNewAccount($user);
        }
        return $this->render(self::PATH_REGISTER_TPL.'particular.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param AppSecurity $appSecurity
     * @return Response|RedirectResponse
     * @Route("/compte/pro", name="register_account_pro", methods={"GET", "POST"})
     */
    public function registerPro(Request $request, AppSecurity $appSecurity): Response
    {
        if($this->getUser())
        {
            return $appSecurity->redirectDashboard();
        }
        $user = new User();
        $form = $this->createForm(RegistrationProType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $appSecurity->createAccount($user,'pro');
            return $this->paramsRedirectNewAccount($user);
        }
        return $this->render(self::PATH_REGISTER_TPL.'pro.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @param User $user
     * @return RedirectResponse
     */
    private function paramsRedirectNewAccount(User $user): RedirectResponse
    {
        return $this->redirectToRoute(
            self::CONFIRM_ACCOUNT_CREATE, ['id' => $user->getId(), 'token' => $user->getConfirmationToken()]
        );
    }



}
