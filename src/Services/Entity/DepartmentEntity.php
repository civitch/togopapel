<?php


namespace App\Services\Entity;


use App\Entity\Department;

class DepartmentEntity extends Entity
{
    /**
     * Traitement de la persistence de l'objet Département
     *
     * @param Department $department
     */
    public function persistEntity(Department $department)
    {
        $this->em->persist($department);
        $this->em->flush();
        $this->flashBag->add('success', 'Ajout département avec succès');
    }
}
