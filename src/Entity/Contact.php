<?php


namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    /**
     * @Assert\NotBlank(message="Veuillez saisir votre nom!")
     * @Assert\Length(min=2, max=100)
     */
    private ?string $nom = null;

    /**
     * @Assert\NotBlank(message="Veuillez saisir votre adresse mail!")
     * @Assert\Email(message="Veuillez saisir une adresse mail valide")
     */
    private ?string $email = null;

    /**
     * @Assert\Regex(
     *  pattern="/[0-9]{8,14}/"
     * )
     */
    private ?string $tel = null;

    /**
     * @Assert\NotBlank(message="Veuillez choisir une option!")
     */
    private ?string $option = null;

    /**
     * @Assert\NotBlank(message="Veuillez décrire un contenu!")
     */
    private ?string $message = null;

    /**
     * @return string
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     * @return self
     */
    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getTel(): ?string
    {
        return $this->tel;
    }

    /**
     * @param string $tel
     * @return self
     */
    public function setTel(?string $tel): ?self
    {
        $this->tel = $tel;
        return $this;
    }

    /**
     * @return string
     */
    public function getOption(): ?string
    {
        return $this->option;
    }

    /**
     * @param string $option
     * @return self
     */
    public function setOption(string $option): self
    {
        $this->option = $option;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return self
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }




}
