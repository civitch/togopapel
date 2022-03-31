<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 */
class Notification
{
    const ROLES = [
        'annonce'             => 1,
        'validation_annonce'  => 2,
        'credit'              => 3,
        'validation_credit'   => 4,
        'user_pack'           => 5,
        'wallet'              => 6,
        'pack_expire'         => 7,
        'send_message'        => 8,
        'message_exchange'    => 9,
        'user_create_account' => 10,
        'user_valid_account'  => 11,
        'user_edit_account'   => 12,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $message;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NotificationUser", mappedBy="notification")
     */
    private $notificationUsers;

    /**
     * @ORM\Column(type="integer")
     */
    private $role;

    public function __construct()
    {
        $this->notificationUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return Collection|NotificationUser[]
     */
    public function getNotificationUsers(): Collection
    {
        return $this->notificationUsers;
    }

    public function addNotificationUser(NotificationUser $notificationUser): self
    {
        if (!$this->notificationUsers->contains($notificationUser)) {
            $this->notificationUsers[] = $notificationUser;
            $notificationUser->setNotification($this);
        }

        return $this;
    }

    public function removeNotificationUser(NotificationUser $notificationUser): self
    {
        if ($this->notificationUsers->contains($notificationUser)) {
            $this->notificationUsers->removeElement($notificationUser);
            // set the owning side to null (unless already changed)
            if ($notificationUser->getNotification() === $this) {
                $notificationUser->setNotification(null);
            }
        }

        return $this;
    }

    public function getRole(): ?int
    {
        return $this->role;
    }

    public function setRole(int $role): self
    {
        $this->role = $role;

        return $this;
    }


}
