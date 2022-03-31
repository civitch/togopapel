<?php


namespace App\Controller\Core;

use App\Entity\ChangePassword;
use App\Entity\Profile;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * Class ProfileController
 * @package App\Controller\Core
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 * @Route("/corporate")
 */
class ProfileController extends AbstractController
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/profile", name="profile_corporate_user", methods={"GET", "POST"})
     */
    public function displayProfile(Request $request, UserRepository $userRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $profile = new Profile();
        $errors = [];
        $currentUser = $this->getUser();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        $changePassword = new ChangePassword();
        $formEditPassword = $this->createForm(ChangePasswordType::class, $changePassword);
        $formEditPassword->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findUserEditProfile($this->getUser()->getUsername(), $form->get('email')->getData());
            if ($user) {
                $errors['email'] = 'Cette adresse existe déjà ';
                return $this->render('Core/Profile/index.html.twig', ['errors' => $errors, 'form' => $form->createView()]);
            }

            /**
             * @var User $currentUser
             */
            $currentUser->setName($profile->getName());
            $currentUser->setFirstname($profile->getFirstname());
            $currentUser->setEmail($profile->getEmail());
            $currentUser->setVille($profile->getVille());
            $currentUser->setCivility($profile->getCivility());
            $currentUser->setAdresse($profile->getAdresse());
            $currentUser->setTel($profile->getTel());
            $em->flush();
            $this->addFlash('success', 'Votre profil a été modifié avec succès!');
            return $this->redirectToRoute('profile_corporate_user');
        }


        if($formEditPassword->isSubmitted() && $formEditPassword->isValid())
        {
            $currentUser->setPassword($this->passwordEncoder->encodePassword($currentUser, $changePassword->getNewPassword()));
            $em->flush();
            $this->addFlash('success', 'Votre mot de passe a été modifié avec succès!');
            return $this->redirectToRoute('profile_corporate_user');
        }

        $options = ['formEditPassword' => $formEditPassword->createView(), 'form' => $form->createView(),'user' => $this->getUser(), 'errors' => $errors];
        return $this->render('Core/Profile/index.html.twig', $options);
    }





}
