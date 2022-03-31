<?php


namespace App\Controller\Security;


use App\Entity\User;
use App\Services\App\AppSecurity;
use App\Services\Mail\AppMail;
use App\Services\Notification\UserNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConfirmationController
 * @package App\Controller\Security
 * @Route("/confirmation")
 */
class ConfirmationController extends AbstractController
{
    /**
     * Message de confirmation après la création d'un compte particulier et professionnel
     * @return RedirectResponse|Response
     * @throws \Exception
     * @Route("/creation/compte", name="confirm_message_account_create", methods={"GET"})
     */
    public function confirmRegisterMessage(AppSecurity $appSecurity)
    {
        $user = $appSecurity->confirmRegister();
        /** @var User $user */
        if($user){
            $interval = (new \DateTime())->getTimestamp() - $user->getCreatedAt()->getTimestamp();
            if($interval <= 120 && $interval > 0)
            {
                return $this->render('Security/Confirmation/register.html.twig');
            }
            return $this->redirectToRoute('home_project');
        }
        return $this->redirectToRoute('home_project');
    }


    /**
     * Message de confirmation après la demande de modification du mot de passe par mail
     * @Route(
     *     "/reset/lost-password",
     *     name="confirm_message_reset_password",
     *     methods={"GET"}
     * )
     */
    public function confirmResetPasswordMessage()
    {
        if(!isset($_GET['id']) && !isset($_GET['token'])) {
            return $this->redirectToRoute('home_project');
        }
        $em = $this->getDoctrine()->getManager();
        $id = (int) $_GET['id'];
        $token = $_GET['token'];
        $user = $em->getRepository(User::class)->findByIdAndToken($id, $token, true);
        /** @var User $user */
        if($user)
        {
            $interval = (new \DateTime())->getTimestamp() - $user->getResetAt()->getTimestamp();
            if($interval <= 120 && $interval > 0)
            {
                return $this->render('Security/Confirmation/forgetPassword.html.twig');
            }

            return $this->redirectToRoute('home_project');
        }
        return $this->redirectToRoute('home_project');
    }


    /**
     * Lorsque l'utilisateur confirme son compte après l'avoir créé
     * @Route("/account/{id}", name="confirm_account",  requirements={"id" = "\d+"})
     */
    public function confirmAccount($id, UserNotification $userNotification, AppMail $appMail)
    {
        if(!isset($_GET['confirm_token'])){
            return $this->redirectToRoute('home_project');
        }
        $id = (int) $id;
        $confirm_token = $_GET['confirm_token'];
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findByIdAndToken($id, $confirm_token);
        /** @var User $user */
        if(!$user){
            return $this->redirectToRoute('home_project');
        }
        #vérifier si la date de création de compte ne dépasse passe pas une heure de temps
        $interval = (new \DateTime())->getTimestamp() - $user->getCreatedAt()->getTimestamp();

        if($interval <= 3600 && $interval > 0)
        {
            $user
                ->setConfirmationToken(null)
                ->setConfirmationAt(new \DateTime())
                ->setEnabled(true)
            ;
            $em->flush();
            $appMail->confirmAccount($user->getEmail());
            $userNotification->accountCreated(
                'Confirmation de compte',
                "{$user->getEmail()} vient de confirmer son compte!",
                true
            );
            return $this->render('Security/Confirmation/success_account.html.twig');
        }
        else{
            $em->remove($user);
            $em->flush();
            return $this->render('Security/Reset/tokenExpire.html.twig');
        }
    }

}
