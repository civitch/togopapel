<?php


namespace App\Services\App;


use App\Entity\Department;
use App\Entity\User;
use App\Repository\DepartmentRepository;
use App\Services\Mail\AppMail;
use App\Services\Notification\UserNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

abstract class App
{
    /**
     * @var EntityManagerInterface $em
     */
    protected $em;

    /**
     * @var $roles
     */
    private $roles;

    private \App\Repository\DepartmentRepository $departments;

    /**
     * @var UserPasswordHasherInterface
     */
    protected $passwordEncoder;

    /**
     * @var AppMail
     */
    protected $appMail;

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var UserNotification
     */
    protected $userNotification;

    public function __construct(
        EntityManagerInterface $em,
        DepartmentRepository $departmentRepository,
        UserPasswordHasherInterface $passwordEncoder,
        AppMail $appMail,
        Security $security,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig,
        UserNotification $userNotification
    )
    {
        $this->em = $em;
        $this->departments = $departmentRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->appMail = $appMail;
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->userNotification = $userNotification;
    }

    /**
     * Retourn le role en fonction du titre
     * @param string $title
     * @return mixed|null
     */
    protected function getRoles(string $title)
    {
        $this->roles = [
            'maintenance' => 'ROLE_MAINTENANCE',
            'super_admin' => 'ROLE_SUPER_ADMIN',
            'admin'       => 'ROLE_ADMIN',
            'moderateur'  => 'ROLE_MODERATEUR',
            'particular'  => 'ROLE_PARTICULIER',
            'pro'         => 'ROLE_PROFESSIONNEL'
        ];
        return (isset($this->roles[$title])) ? $this->roles[$title] : null;
    }


    /**
     * Retourne le départment en fonction du rôle
     * @param $title
     * @return Department|null
     */
    public function getRolesApp(string $title): ?Department
    {
        $role = $this->getRoles($title);
        if(!is_null($role)){
            return $this->departments->findOneBy(['role' => $this->getRoles($title)]);
        }
        return null;
    }


}
