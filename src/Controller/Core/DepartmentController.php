<?php


namespace App\Controller\Core;

use App\Entity\Department;
use App\Form\DepartmentType;
use App\Repository\DepartmentRepository;
use App\Services\Entity\DepartmentEntity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DepartmentController
 * @package App\Controller\Core
 * @Route("/corporate")
 */
class DepartmentController extends AbstractController
{
    /**
     * @IsGranted("ROLE_MAINTENANCE")
     * @Route("/department", name="department_corporate", methods={"GET", "POST"})
     */
    public function index(Request $request, DepartmentRepository $departmentRepository, DepartmentEntity $departmentEntity)
    {
        $department = new Department();
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $departmentEntity->persistEntity($department);
            return $this->redirectToRoute('department_corporate');
        }
        return $this->render('Core/Department/index.html.twig', $departmentEntity->options($form, $departmentRepository->findAll()));
    }
}
