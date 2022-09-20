<?php


namespace App\Controller\Security;


use App\Entity\ForgetPassword;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ForgetPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Services\App\AppSecurity;
use App\Services\Mail\AppMail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ResetController
 * @package App\Controller\Security
 */
#[Route(path: '/reset')]
class ResetController extends AbstractController
{

    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route(path: '/lost-password', name: 'password_forget', methods: ['GET', 'POST'])]
    public function forgetPassword(Request $request, AppSecurity $appSecurity, AppMail $appMail)
    {
        if ($this->getUser() !== null) {
            return $this->redirectToRoute('main_dashboard');
        }
        $errors = [];
        $forget = new ForgetPassword();
        $form = $this->createForm(ForgetPasswordType::class, $forget);
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            /** @var User $user */
            $user = $em->getRepository(User::class)->forgetPassword($forget->getEmail());
            if(!is_null($user))
            {
                $user
                    ->setResetToken($appSecurity->randomToken(120))
                    ->setResetAt(new \DateTime())
                ;
                $em->flush();
                $appMail->reset($user->getEmail(), $user->getId(), $user->getResetToken());
                return $this->redirectToRoute(
                    'confirm_message_reset_password', [
                        'id'  => $user->getId(),
                        'token' => $user->getResetToken()
                ]);
            }
            $errors['email'] = "Imposible de réinitialiser le mot de passe pour ce compte!";
            return $this->render("Security/Reset/forget.html.twig", [
                'form'  => $form->createView(),
                'error' => $errors
            ]);
        }
        return $this->render("Security/Reset/forget.html.twig", [
            'form' => $form->createView(),
            'error' => $errors
        ]);
    }


    #[Route(path: '/password/{id}', name: 'password_reset', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function confirmResetPassword($id, Request $request, UserRepository $userRepository)
    {
        if ($this->getUser() !== null) {
            return $this->redirectToRoute('main_dashboard');
        }
        if(!isset($_GET['reset_token']))
        {
            return $this->redirectToRoute('home_project');
        }
        $token = $_GET['reset_token'];
        $id = (int) $id;
        $user = $userRepository->checkIfUserCanResetPassword($id, $token);
        $resetPassword = new ResetPassword();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createform(ResetPasswordType::class, $resetPassword);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user
                ->setPassword($this->passwordEncoder->hashPassword($user, $form->getData()->getPassword()))
                ->setResetToken(null)
                ->setResetAt(null)
            ;
            $em->flush();
            $this->addFlash('success', 'Votre mot de passe a été modifié avec succès!');
            return $this->redirectToRoute('admin_auth');
        }
        if(!is_null($user)){
            $interval = (new \DateTime())->getTimestamp() - $user->getResetAt()->getTimestamp();
            if($interval <= 3600 && $interval > 0){
                return $this->render('Security/Reset/resettingPassword.html.twig', ['form' => $form->createView()]);
            }
            else{
                $user
                    ->setResetToken(null)
                    ->setResetAt(null)
                ;
                $em->flush();
                return $this->render('Security/Reset/tokenExpire.html.twig');
            }
        }else{
            return $this->redirectToRoute('home_project');
        }
    }






}
