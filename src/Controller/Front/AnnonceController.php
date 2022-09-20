<?php


namespace App\Controller\Front;


use App\Entity\AnnonceSearch;
use App\Entity\Categorie;
use App\Entity\Region;
use App\Entity\Rubrique;
use App\Entity\Ville;
use App\Form\AnnonceSearchType;
use App\Repository\AnnonceRepository;
use App\Services\Entity\AnnonceEntity;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

/**
 * Controller de gestion d'affichage des annonces au niveau frontend
 * Class AnnonceController
 * @package App\Controller\Front
 *
 */
class AnnonceController extends AbstractController
{
    const LIMIT_PAGES = 30;
    const PARAMETER_FILTER = 1;
    const PARAMETER_VALUE = 'page';
    const REDIRECT_ERROR_PAGE = 'home_project';

    private \App\Services\Entity\AnnonceEntity $annonceService;

    public function __construct(AnnonceEntity $annonceService)
    {
        $this->annonceService = $annonceService;
    }

    /**
     * Liste les annonces
     *
     * @param Request $request
     * @param AnnonceRepository $annonceRepository
     * @return Response
     */
    #[Route(path: '/annonces', name: 'liste_annonce_front', methods: ['GET', 'POST'])]
    public function liste(Request $request, AnnonceRepository $annonceRepository): Response
    {

        $search = new AnnonceSearch();
        $form = $this->createForm(AnnonceSearchType::class, $search);
        $form->handleRequest($request);
        #$search->setParticulier(true);
        $search->setType(true);
        if(empty($request->query->all())){
            $annonces = $annonceRepository->getSearchAnnnonce(
                $search,
                $request->query->getInt(self::PARAMETER_VALUE, self::PARAMETER_FILTER),
                self::LIMIT_PAGES,
                true
            );
            $annonceLength = count($annonceRepository->findAll());

        }
        else{
            $annonces=[];
            $annonces = $annonceRepository->getSearchAnnnonce(
                $search,
                $request->query->getInt(self::PARAMETER_VALUE, self::PARAMETER_FILTER),
                self::LIMIT_PAGES,
                true
            );
            $annonceLength = count($annonces);
        }

        $options = [
            'info'       => $_GET,
            'form'       => $form->createView(),
            'annonces'   => $annonces
        ];
        
        $options["taille"]=$annonceLength;
        #return  new JsonResponse($request->query->getInt(self::PARAMETER_VALUE, self::PARAMETER_FILTER));
        return $this->render(AnnonceEntity::LIST_ANNONCES, $options);
    }
    

    /**
     * Liste les annonces par catégorie
     *
     * @param Request $request
     * @param AnnonceRepository $annonceRepository
     * @param string $slug
     * @return RedirectResponse|Response
     */
    #[Route(path: '/annonces/categorie/{slug}', name: 'liste_annonce_categorie_front', methods: ['GET'], requirements: ['slug' => '[a-z0-9\-]*'])]
    public function listeByCategory(Request $request, AnnonceRepository $annonceRepository, string $slug): Response
    {
        $em = $this->getDoctrine()->getManager();
        $categorie = $em->getRepository(Categorie::class)->findOneBy(['slug' => $slug]);
        if(!$categorie instanceof \App\Entity\Categorie)
        {
           return $this->redirectToRoute(self::REDIRECT_ERROR_PAGE);
        }
        $annonces = $annonceRepository->getAnnoncesByCatSlug(
            $request->query->getInt(self::PARAMETER_VALUE, self::PARAMETER_FILTER),
            $slug,
            self::LIMIT_PAGES
        );
        $annonceLength = count($annonces);
        return $this->annoncesByInfos($request, $annonces,$annonceLength);
    }


    /**
     * Liste des annonces par ville
     *
     * @param Request $request
     * @param AnnonceRepository $annonceRepository
     * @param string $slug
     * @return Response
     */
    #[Route(path: '/annonces/ville/{slug}', name: 'liste_annonce_ville_front', methods: ['GET'], requirements: ['slug' => '[a-z0-9\-]*'])]
    public function listeByVille(Request $request, AnnonceRepository $annonceRepository, string $slug): Response
    {
        $em = $this->getDoctrine()->getManager();
        $ville = $em->getRepository(Ville::class)->findOneBy(['slug' => $slug]);
        if(!$ville instanceof \App\Entity\Ville)
        {
            return $this->redirectToRoute(self::REDIRECT_ERROR_PAGE);
        }
        $annonces = $annonceRepository->getAnnoncesByVilleSlug(
            $request->query->getInt(self::PARAMETER_VALUE, self::PARAMETER_FILTER),
            $slug,
            self::LIMIT_PAGES);
        return $this->annoncesByInfos($request, $annonces);
    }


    /**
     * Liste des annonces par région
     *
     * @param Request $request
     * @param AnnonceRepository $annonceRepository
     * @param string $slug
     * @return Response
     */
    #[Route(path: '/annonces/region/{slug}', name: 'liste_annonce_region_front', methods: ['GET'], requirements: ['slug' => '[a-z0-9\-]*'])]
    public function listeByRegion(Request $request, AnnonceRepository $annonceRepository, string $slug): Response
    {
        $em = $this->getDoctrine()->getManager();
        $region = $em->getRepository(Region::class)->findOneBy(['slug' => $slug]);
        if(!$region instanceof \App\Entity\Region)
        {
            return $this->redirectToRoute(self::REDIRECT_ERROR_PAGE);
        }
        $annonces = $annonceRepository->getAnnoncesBySlugRegion(
            $request->query->getInt(self::PARAMETER_VALUE, self::PARAMETER_FILTER),
            $slug,
            self::LIMIT_PAGES);
        return $this->annoncesByInfos($request, $annonces);
    }

    /**
     * Liste des annonces par rubriques
     *
     * @param Request $request
     * @param AnnonceRepository $annonceRepository
     * @param string $slug
     * @return Response
     */
    #[Route(path: '/annonces/rubrique/{slug}', name: 'liste_annonce_rubrique_front', methods: ['GET'], requirements: ['slug' => '[a-z0-9\-]*'])]
    public function listeByRubrique(Request $request, AnnonceRepository $annonceRepository, string $slug): Response
    {
        $em = $this->getDoctrine()->getManager();
        $rubrique = $em->getRepository(Rubrique::class)->findOneBy(['slug' => $slug]);
        if(!$rubrique instanceof \App\Entity\Rubrique)
        {
            return $this->redirectToRoute(self::REDIRECT_ERROR_PAGE);
        }
        $annonces = $annonceRepository->getAnnoncesBySlugRubrique(
            $request->query->getInt(self::PARAMETER_VALUE, self::PARAMETER_FILTER),
            $slug,
            self::LIMIT_PAGES
        );
        return $this->annoncesByInfos($request, $annonces);
    }

    /**
     * Affiche une annonce
     *
     * @param string $slug
     * @return Response
     */
    #[Route(path: '/annonce/{slug}', name: 'annonce_info')]
    public function show(string $slug, AnnonceRepository $annonceRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $annonce = $annonceRepository->findOneBy(['slug' => $slug]);
        $annonce->setNbrVue($annonce->getNbrVue()+1);
        $em->persist($annonce);
        $em->flush();
        if(!$annonce instanceof \App\Entity\Annonce)
        {
            return $this->redirectToRoute('liste_annonce_front');
        }
        $options = [
            'annonce' => $annonce,
            'cats' => $annonceRepository->relatedAnnoncesByCategorie($annonce),
            'annoncesOwner' => $annonceRepository->getAnnonceEnabledByUser($annonce->getUser())
        ];
       // dump($annonce->getUser());
        return $this->render('Front/Annonce/show.html.twig', $options);
    }


    /**
     * @param Request $request
     * @param PaginationInterface $annonces
     * @return Response
     */
    private function annoncesByInfos(Request $request, PaginationInterface $annonces,int $annonceLength=0): Response
    {
        $options = $this->annonceService->annoncesByInfos($request, $annonces);
        $options["taille"]=$annonceLength;
        return $this->render(AnnonceEntity::LIST_ANNONCES, $options);
    }






}
