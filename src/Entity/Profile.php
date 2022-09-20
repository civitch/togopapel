<?php

namespace App\Entity;


use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;


class Profile
{
    /**
     * @Assert\Choice({"madame", "monsieur"})
     */
    private $civility;

    /**
     * @Assert\NotBlank(message="L'email doit être saisi")
     * @Assert\Email(message = "L'adresse mail {{ value }} n'est pas valide")
     */
    private $email;

    /**
     * @SecurityAssert\UserPassword(message="Le mot de passe saisi ne correspond pas au vôtre!")
     * @Assert\NotBlank(message="Le mot de passe doit être saisi")
     */
    private $password;
    private $name;
    private $adresse;

    /**
     * @Assert\Length(max = 100, maxMessage="Le n° de tél doit pas excéder {{ limit }} caractères", allowEmptyString = false)
     * @Assert\Regex(pattern="/^[0-9 -.]+$/", message="Le numéro de téléphone n'est pas valide!")
     */

    private $tel;
    private $society;
    private $siren;
    private $firstname;
    private $rubrique;
    private $ville;
    private $telIndicatif;
    private ?string $description = null;


    public function getEmail(): ?string
    {
        return $this->email;
    }


    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }


    public function getName(): ?string
    {
        return $this->name;
    }


    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }


    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }


    public function getCivility(): ?string
    {
        return $this->civility;
    }

    public function setCivility(?string $civility): self
    {
        $this->civility = $civility;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTelIndicatif(): ?IndicatifPays
    {
        return $this->telIndicatif;
    }

    /**
     * @param mixed $telIndicatif
     */
    public function setTelIndicatif(?IndicatifPays $telIndicatif): self
    {
        $this->telIndicatif = $telIndicatif;
        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getSociety(): ?string
    {
        return $this->society;
    }

    public function setSociety(?string $society): self
    {
        $this->society = $society;

        return $this;
    }

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function setSiren(?string $siren): self
    {
        $this->siren = $siren;

        return $this;
    }

    public function getRubrique(): ?Rubrique
    {
        return $this->rubrique;
    }

    public function setRubrique(?Rubrique $rubrique): self
    {
        $this->rubrique = $rubrique;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Profile
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }



}
