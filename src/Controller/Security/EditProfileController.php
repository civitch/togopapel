<?php


namespace App\Controller\Security;


use App\Entity\ChangePassword;
use App\Entity\Profile;
use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use App\Repository\AnnonceRepository;
use App\Repository\UserRepository;
use App\Services\App\AppSecurity;
use App\Services\Notification\UserNotification;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Permet de modifier les informations d'un compte user
 * Class EditProfileController
 * @package App\Controller\Security
 * @Route("/profile")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class EditProfileController extends AbstractController
{
    private $em;
    private $appSecurity;

    public function __construct(EntityManagerInterface $em, AppSecurity $appSecurity)
    {
        $this->em = $em;
        $this->appSecurity = $appSecurity;
    }

    /**
     * @Route("/edit/mot-de-passe", name="edit_profile_password", methods={"GET", "POST"})
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param UserNotification $notification
     * @return RedirectResponse|Response
     */
    public function editPassword(
        Request $request,
        UserPasswordHasherInterface $passwordEncoder,
        UserNotification $notification
    ): Response
    {
        $user = $this->getUser();
        $changePassword = new ChangePassword();
        $form = $this->createForm(ChangePasswordType::class, $changePassword);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->hashPassword($user, $changePassword->getNewPassword()));
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $notification->accountEdit(
                'Modification de mot de passe',
                'Mot de passe modifié avec succès',
                $user
            );
            return $this->redirectToRoute('account_profile');
        }
        return $this->render('Security/Reset/editPassword.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Mise à jour du profil
     *
     * @Route("/edit", name="edit_profile", methods={"GET", "POST"})
     * @param UserRepository $userRepository
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function editProfile(UserRepository $userRepository, Request $request): Response
    {
        $profile = new Profile();
        $form = $this->createForm(ProfileType::class, $profile);
        $errors = [];
        $form->handleRequest($request);
        $telIndicatif = $form->get('telIndicatif')->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findUserEditProfile($this->getUser()->getUsername(), $form->get('email')->getData());
            if ($user) {
                $errors['email'] = 'Cette adresse existe déjà ';
                return $this->render('Security/Profile/index.html.twig', ['errors' => $errors, 'form' => $form->createView()]);
            }
            // Mise à jour du profile
            $this->appSecurity->setProfile($profile);
            return $this->redirectToRoute('account_profile');
        }
        return $this->render('Security/Profile/edit.html.twig', ['errors' => $errors, 'form' => $form->createView()]);
    }

    /**
     * @param AnnonceRepository $annonceRepository
     * @param Request $request
     * @return Response
     * @Route("/compte", name="account_profile", methods={"GET"})
     */
    public function getProfile(AnnonceRepository $annonceRepository, Request $request): Response
    {
        $options = [
            'annonces' => $annonceRepository->getAnnonceEnabledByUserPaginate(
                $this->getUser(),
                $request->query->getInt('page', 1),
                10
            )
        ];
        return $this->render('Security/Profile/index.html.twig', $options);
    }


}
