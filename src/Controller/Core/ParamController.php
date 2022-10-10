<?php

namespace App\Controller\Core;

use App\DataTables\ParamDataTable;
use App\Entity\Param;
use App\Exception\BadRequestHttpException;
use App\Form\ParamType;
use App\Repository\ParamRepository;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/corporate/param')]
class ParamController extends AbstractController
{

    public function __construct(
        private ParamRepository $paramRepository,
        private DataTableFactory $dataTableFactory
    )
    {}

    #[Route('/', name: 'app_core_param')]
    public function index(
        Request $request,
        ParamRepository $paramRepository
    ): Response
    {

        #Form builder
        $param = new Param();

        # Generate Param edit link
        $this->formOptions['action'] = $this->generateUrl('app_core_param');
        
        $form = $this->createForm(
            ParamType::class, 
            $param,
            $this->formOptions
        )
        ->add('submitButton', SubmitType::class, [
            'label' => 'Ajouter',
        ]);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $paramRepository->add($data, true);
            $this->addFlash('success', 'Ajout effectuer avec succès');
        }
        #End Generation Form

        # DataTable Loader   
        $dataTableTypeOptions = [
            'dataTableName'         => 'paramDataTable',
            'searchable'            => true,
        ];

        $table = $this->dataTableFactory->createFromType(
            ParamDataTable::class,
            $dataTableTypeOptions,
            ['pageLength' => 25, 'order' => [[0, 'asc']]],
        )
        ->handleRequest($request);
        
        if($table->isCallback())
        {
            return $table->getResponse();
        }
        
        #End DataTable Loader

        return $this->render('core/param/index.html.twig', [
            'datatable' => $table,
            'form'     => $form->createView(),
        ]);

    }

    #[Route('/{id}', name : 'app_core_param_edit', methods : ['GET','POST'])]
    public function edit(
        Param $param, 
        Request $request, 
        ParamRepository $paramRepository
    )
    {
        # Begin form 
        # Generate Param edit link
        $this->formOptions['action'] = $this->generateUrl('app_core_param_edit',['id' => $param->getId()]);
        $form = $this->createForm(
            ParamType::class, 
            $param,
            $this->formOptions
        )
        ->add('submitButton', SubmitType::class, [
            'label' => 'Modifier',
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $paramRepository->add($data, true);

            $this->addFlash('success', 'Mise à jour effectuer avec succès');
        }
        # End form

        
        # DataTable Loader   
        $dataTableTypeOptions = [
            'dataTableName'         => 'paramDataTable',
            'searchable'            => true,
        ];

        $table = $this->dataTableFactory->createFromType(
            ParamDataTable::class,
            $dataTableTypeOptions,
            ['pageLength' => 25, 'order' => [[0, 'asc']]],
        )
        ->handleRequest($request);
        
        if($table->isCallback())
        {
            return $table->getResponse();
        }
        
        #End DataTable Loader

        return $this->render('core/param/index.html.twig', [
            'datatable' => $table,
            'form' => $form->createView()
        ]);

    }

    #[Route('delete/{id}', name : "app_core_param_delete", methods : ['DELETE'])]
    public function delete(
        Request $request, 
        Param $param, 
        ParamRepository $paramRepository
    )
    {

        if(! $request->isXmlHttpRequest())
        {
            new BadRequestHttpException("La requête ne provient pas de la bonne source.");
        }

        $paramRepository->remove($param, true);

        return new Response("Le paramètre a été supprimé avec succès");

    }

}
