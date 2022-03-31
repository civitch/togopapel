<?php


namespace App\Controller\Core;


use App\Entity\Region;
use App\Form\RegionType;
use App\Repository\RegionRepository;
use App\Services\Entity\RegionEntity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class RegionController
 * @package App\Controller\Core
 * @Route("/corporate")
 */
class RegionController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/region",  name="region_corporate", methods={"GET", "POST"})
     */
    public function index(Request $request, RegionEntity $regionEntity, RegionRepository $regionRepository)
    {
        $region = new Region();
        $form = $this->createForm(RegionType::class, $region);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $regionEntity->persistEntity($region);
            return $this->redirectToRoute('region_corporate');
        }
        return $this->render('Core/Region/index.html.twig', $regionEntity->options($form, $regionRepository->findAll()));
    }
}
