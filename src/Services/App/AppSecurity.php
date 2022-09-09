<?php


namespace App\Services\App;


use App\Entity\Profile;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AppSecurity extends App
{
    const DASHBOARD_PATH = 'main_dashboard';
    const HOMEPAGE_PATH = 'home_project';

    public function redirectDashboard()
    {
        return new RedirectResponse($this->urlGenerator->generate(self::DASHBOARD_PATH));
    }

    /**
     * Permet de retourner un token hasher
     *
     * @param $length
     * @return false|string
     */
    public function randomToken($length)
    {
        try {
            return substr(str_shuffle(str_repeat(hash('sha1', bin2hex(random_bytes($length))), $length)), 0, $length);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Retourne le rôle utilisateur
     *
     * @param string $title
     * @return mixed|null
     */
    public function getRole(string $title)
    {
        return $this->getRoles($title);
    }

    /**
     * Fonction permettant de cré un compte
     *
     * @param User $user
     * @param $role string
     */
    public function createAccount(User $user, string $role)
    {
        $dep = $this->getRolesApp($role);
        $user->setConfirmationToken($this->randomToken(100));
        $user->setDepartment($dep);
        $user->addRole($dep->getRole());
        $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPassword()));
        $this->em->persist($user);
        $this->em->flush();
        $this->userNotification->accountCreated(
            "Création de compte {$role}",
            "{$user->getEmail()} vient de créer son compte {$role}"
        );
        $this->appMail->register($user->getEmail(), $user->getId(), $user->getConfirmationToken());
    }


    /**
     * @return RedirectResponse|User|null
     */
    public function confirmRegister(): ?User
    {
        if(!isset($_GET['id']) && !isset($_GET['token']))
        {
            return new RedirectResponse($this->urlGenerator->generate(self::HOMEPAGE_PATH));
        }
        $id = (int) $_GET['id'];
        $token = $_GET['token'];
        $user = $this->em->getRepository(User::class)->findByIdAndToken($id, $token);
        return $user;
    }


    /**
     * Permet de mettre à jour un profil utilisateur
     *
     * @param Profile $profile
     */
    public function setProfile(Profile $profile)
    {
        $currentUser = $this->security->getUser();
        $currentUser->setName($profile->getName());
        $currentUser->setFirstname($profile->getFirstname());
        $currentUser->setEmail($profile->getEmail());
        $currentUser->setSociety($profile->getSociety());
        $currentUser->setVille($profile->getVille());
        $currentUser->setRubrique($profile->getRubrique());
        $currentUser->setSiren($profile->getSiren());
        $currentUser->setCivility($profile->getCivility());
        $currentUser->setAdresse($profile->getAdresse());
        $currentUser->setTel($profile->getTel());
        $currentUser->setDescription($profile->getDescription());
        $currentUser->setIndicatifPays($profile->getTelIndicatif());

        $this->em->flush();
        $this->userNotification->accountEdit(
            'Modification de votre profil',
            'Profil modifié avec succès',
            $currentUser
        );
    }




}
