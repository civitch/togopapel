<?php


namespace App\Services\Entity;



use App\Entity\AnnonceSearch;
use App\Form\AnnonceSearchType;
use Knp\Component\Pager\Pagination\PaginationInterface; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AnnonceEntity extends Entity {

    const LIST_ANNONCES = 'Front/Annonce/list.html.twig';

    /**
     * @param Request $request
     * @param PaginationInterface $annonces
     * @return array
     */
    public function annoncesByInfos(Request $request, PaginationInterface $annonces): array
    {
        $search = new AnnonceSearch();
        $form = $this->form->create(AnnonceSearchType::class, $search);
        $form->handleRequest($request);
        return  ['form' => $form->createView(), 'annonces' => $annonces];
    }




}
