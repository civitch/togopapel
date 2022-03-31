<?php


namespace App\Controller\Front;


 use App\Entity\Contact;
 use App\Form\ContactType;
 use App\Repository\AnnonceRepository;
 use App\Repository\CatRepository;
 use App\Repository\OptionRepository;
 use App\Repository\PostTypeRepository;
 use App\Repository\RubriqueRepository;
 use App\Repository\VilleRepository;
 use App\Services\Mail\AppMail;
 use Doctrine\ORM\NonUniqueResultException;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

 class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home_project", methods={"GET"})
     * @return Response
     */
    public function index() :Response
    {
        return $this->render('Front/Home/index.html.twig');
    }

     /**
      * @param VilleRepository $villeRepository
      * @return Response
      */
     public function carteListe(VilleRepository $villeRepository): Response
     {
         return $this->render('Front/Home/carte.html.twig', ['villes' => $villeRepository->getVillesSorting()]);
     }

    /**
     * Affiche les catégories dans le footer
     * @param RubriqueRepository $rubriqueRepository
     * @return Response
     */
    public function listeRubrique(RubriqueRepository $rubriqueRepository) :Response
    {
        return $this->render('Front/Home/rubrique.html.twig', ['rubriques' => $rubriqueRepository->getAllRubrique()]);
    }

    /**
     * @Route("/contact", name="contact_project", methods={"GET", "POST"})
     */
    public function contact(Request $request, AppMail $appMail)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $appMail->contact($contact->getEmail(), $contact->getOption(), $contact->getMessage());
            $this->addFlash('success', 'Votre email a été envoyé avec succès!');
            return $this->redirectToRoute('admin_auth');
        }
        return $this->render('Front/Home/contact.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param CatRepository $catRepository
     * @return Response
     * @Route("/faq", name="faq_project", methods={"GET"})
     */
    public function faq(CatRepository $catRepository): Response
    {
        return $this->render('Front/Home/faq.html.twig', ['cat' => $catRepository->getCatFaq('faq')]);
    }

    /**
     * @param CatRepository $catRepository
     * @return Response
     * @Route("/conseil_de_securite", name="conseil_security_project", methods={"GET"})
     */
    public function conseilSecurity(CatRepository $catRepository): Response
    {
        return $this->render('Front/Home/conseil.html.twig', ['cat' => $catRepository->getCatFaq('conseil')]);
    }

    /**
     * @param CatRepository $catRepository
     * @return Response
     * @Route("/cgu", name="cgu_project", methods={"GET"})
     */
    public function cgu(CatRepository $catRepository): Response
    {
        return $this->render('Front/Home/cgu.html.twig', ['cat' => $catRepository->getCatFaq('cgu')]);
    }

    /**
     * @return Response
     * @Route("/qui_sommes_nous", name="qui_sommes_nous_project", methods={"GET"})
     * @throws NonUniqueResultException
     */
    public function qui_sommes_nous(PostTypeRepository $postTypeRepository): Response
    {
        return $this->render('Front/Home/who.html.twig', ['content' => $postTypeRepository->postTypeByPage()]);
    }

    /**
     * @return Response
     * @Route("/comment_ca_marche", name="comment_ca_marche", methods={"GET"})
     */
    public function howItWorks(): Response
    {
        return $this->render('Front/Home/howItWorks.html.twig');
    }


    /**
     * Liste les 10 dernières annonces
     * @param AnnonceRepository $annonceRepository
     * @return Response
     */
    public function lastListAnnonce(AnnonceRepository $annonceRepository)
    {
        $annonces = $annonceRepository->lastListAnnonces();
        return $this->render('Front/Home/carouselFront.html.twig', ['annonces' => $annonces]);
    }


    public function listeOptionsHome(OptionRepository $optionRepository)
    {
        return $this->render('Front/Home/option.html.twig', ['options' => $optionRepository->getOptionHome()]);
    }

    public function mainInfoHome(OptionRepository $optionRepository)
    {
        return $this->render('Front/Home/main.html.twig', ['option' => $optionRepository->getOptionHome(true)]);
    }


    


    
}
