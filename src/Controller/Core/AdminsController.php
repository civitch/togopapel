<?php


namespace App\Controller\Core;


use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Services\App\AppSecurity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class AdminsController
 * @package App\Controller\Security\Core
 * @Route("/corporate/admin")
 */
class AdminsController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/liste", name="admin_liste_corporate", methods={"GET", "POST"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function listeAdmins(UserRepository $userRepository)
    {
        $admins = $userRepository->listeAdmins();
        return $this->render('Core/Admins/list.html.twig', ['admins' => $admins]);
    }


    /**
     * @Route("/users", name="users_liste_corporate", methods={"GET", "POST"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function listeUsers(UserRepository $userRepository)
    {
        $admins = $userRepository->listeAdmins(true);
        return $this->render('Core/Users/list.html.twig', ['admins' => $admins]);
    }

    /**
     * @Route("/forgetPassword/{id}", name="forget_password_corporate", requirements={"id" = "\d+"}, methods={"GET", "POST"})
     */
    public function editPassword(User $user, Request $request, AppSecurity $appSecurity)
    {
        if($user->hasRole($appSecurity->getRole('pro')) || $user->hasRole($appSecurity->getRole('particular')))
        {
            $this->addFlash('error', 'Seulement les comptes admin ou modérateur!');
            return $this->redirectToRoute('admin_liste_corporate');
        }
        $em = $this->getDoctrine()->getManager();
        $resetPassword = new ResetPassword();
        $form = $this->createForm(ResetPasswordType::class, $resetPassword);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $user
                ->setPassword($this->passwordEncoder->hashPassword($user, $form->getData()->getPassword()));
            $this->addFlash('success', 'Le mot de passe de '.$user->getEmail().' a été modifié avec succès!');
            $em->flush();
            return $this->redirectToRoute('admin_liste_corporate');
        }
        return $this->render("Core/Admins/editPassword.html.twig", ['form' => $form->createView(), 'user' => $user]);
    }


    public function currentUser()
    {
        return $this->render('Core/Admins/current_user.html.twig', ['user' => $this->getUser()]);
    }



}
