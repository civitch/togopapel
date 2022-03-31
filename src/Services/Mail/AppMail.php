<?php

namespace App\Services\Mail;


use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class AppMail
{
    private $mailer;
    private $senderEmail;

    public function __construct(MailerInterface $mailer, $senderEmail)
    {
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
    }


    /**
     * Mail de création de compte
     *
     * @param string $destEmail
     * @param int $id
     * @param string $token
     */
    public function register(string $destEmail, int $id, string $token)
    {
        $options = [
            'url' => "https://{$_SERVER['SERVER_NAME']}/confirmation/account/{$id}?confirm_token={$token}",
            "pathImage" => "https://{$_SERVER['SERVER_NAME']}/build/img/"
        ];
        $this->tplmail($destEmail, 'Activez votre compte', 'Email/create-account.html.twig', $options);
    }

    /**
     * Mail de onfirmation de compte
     *
     * @param string $destEmail
     */
    public function confirmAccount(string $destEmail)
    {
        $options = [
            "pathImage" => "https://{$_SERVER['SERVER_NAME']}/build/img/",
            'url' => "https://{$_SERVER['SERVER_NAME']}/auth"
        ];
        $this->tplmail($destEmail, 'Votre compte est désormais activé!', 'Email/confirm-account.html.twig',  $options);
    }



    /**
     * Mail de réinitialisation de mot de passe
     * @param string $destEmail
     * @param int $id
     * @param string $token
     */
    public function reset(string $destEmail, int $id, string $token)
    {
        $options = [
            "pathImage" => "https://{$_SERVER['SERVER_NAME']}/build/img/",
            'url' => "https://{$_SERVER['SERVER_NAME']}/reset/password/{$id}?reset_token={$token}"
        ];
        $this->tplmail($destEmail, 'Réinitialisation de votre mot de passe', 'Email/editPasswd.html.twig',  $options);
    }


    /**
     * Template de mail par défaut
     *
     * @param string $destEmail
     * @param string $subject
     * @param string $path
     * @param array $options
     */
    private function tplmail(string $destEmail, string $subject, string $path, $options = [])
    {
        $email = (new TemplatedEmail())
            ->from($this->senderEmail)
            ->to($destEmail)
            ->subject($subject)
            ->htmlTemplate($path)
            ->context($options)
        ;
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            echo $e->getMessage();
        }
    }


    public function sendEmailApp(string $destEmail, string $subject, string $body)
    {
        $email = (new Email())
            ->from($this->senderEmail)
            ->to($destEmail)
            ->subject($subject)
            ->text($body)
        ;
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            echo $e->getMessage();
        }
    }

    public function registerAdmin(string $destEmail, int $id, string $token)
    {
        $email = (new Email())
            ->from($this->senderEmail)
            ->to($destEmail)
            ->subject('Création de compte')
            ->text("https://{$_SERVER['SERVER_NAME']}/corporate/confirm/account/{$token}"
            )
        ;
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            echo $e->getMessage();
        }
    }


    /**
     * Mail d'envoie de message contact
     *
     * @param string $contactEmail
     * @param string $subject
     * @param string $content
     */
    public function contact(string $contactEmail, string $subject, string $content)
    {
        $email = (new Email())
            ->from($this->senderEmail)
            ->to($this->senderEmail)
            ->subject($subject)
            ->text($content)
        ;
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            echo $e->getMessage();
        }
    }



}
