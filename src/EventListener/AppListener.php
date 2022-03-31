<?php


namespace App\EventListener;


use App\Services\App\AppSecurity;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

abstract class AppListener
{
    /**
     * @var Environment $twig
     */
    protected $twig;

    /**
     * @var Security $security
     */
    protected $security;


    /**
     * @var AppSecurity $appSecurity
     */
    protected $appSecurity;

    public function __construct(Environment $twig, Security $security, AppSecurity $appSecurity)
    {
        $this->twig = $twig;
        $this->security = $security;
        $this->appSecurity = $appSecurity;
    }
}
