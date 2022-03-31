<?php


namespace App\Controller\Core;

use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\Register\RegisterAdminType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Services\App\AppSecurity;
use App\Services\Mail\AppMail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * Class RegisterController
 * @package App\Controller\Security\Core
 * @Route("/corporate")
 */
class RegisterController extends AbstractController
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/register", name="register_corporate", methods={"GET", "POST"})
     */
    public function register(Request $request, AppSecurity $appSecurity, AppMail $appMail)
    {
        $user = new User();
        $form = $this->createForm(RegisterAdminType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            /** @var User $user */
            $user->addRole($form->get('department')->getData()->getRole());
            $user->setPassword($this->passwordEncoder->encodePassword($user, $appSecurity->randomToken(50)));
            $user->setConfirmationToken($appSecurity->randomToken(200));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $appMail->registerAdmin($user->getEmail(), $user->getId(), $user->getConfirmationToken());
            $this->addFlash('success', 'Compte créé avec succès');
            return $this->redirectToRoute('admin_liste_corporate');
        }
        return $this->render('Core/Register/index.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @Route("/confirm/account/{token}", name="confirm_account_corporate", methods={"GET", "POST"}, requirements={"token" = ".+"})
     */
    public function confirmAccount(string $token, Request $request, UserRepository $userRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $userRepository->checkUserAdmin($token);
        $resetPassword = new ResetPassword();
        $form = $this->createform(ResetPasswordType::class, $resetPassword);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user
                ->setPassword($this->passwordEncoder->encodePassword($user, $form->getData()->getPassword()))
                ->setConfirmationToken(null)
                ->setConfirmationAt(new \DateTime())
                ->setEnabled(true)
            ;
            $em->flush();
            return $this->redirectToRoute('admin_auth');
        }
        if(!is_null($user)){
            return $this->render('Core/Register/confirmAccount.html.twig', ['form' => $form->createView()]);
        }
        else{
            return $this->redirectToRoute('home_project');
        }
    }


}
