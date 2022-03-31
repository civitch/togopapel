<?php

namespace App\Services\Entity;


use App\Services\Notification\PackNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

abstract class Entity{

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var FormFactoryInterface
     */
    protected $form;


    /**
     * @var FlashBagInterface
     */
    protected $flashBag;


    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var PackNotification
     */
    protected $packNotification;

    public function __construct(
        EntityManagerInterface $em,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig,
        PackNotification $packNotification
    )
    {
        $this->em = $em;
        $this->form = $formFactory;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->packNotification = $packNotification;
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    public function options(FormInterface $form, $entities): array
    {
        return ['form' => $form->createView(), 'entities' => $entities];
    }

}
