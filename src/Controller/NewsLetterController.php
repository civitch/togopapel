<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\NewsLetterTemplate;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
/**
 * Class NewsLetterController
 * @package App\Controller
 * @Route("/corporate/news-letter")
 */
    class NewsLetterController extends AbstractController
{
    private $mailer;
    private $senderEmail;
    public function __construct(MailerInterface $mailer, $senderEmail)
    {
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
    }
    /**
     * @Route("/", name="news_letter")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $templates=$em->getRepository(NewsLetterTemplate::class)->findAll();
        return $this->render('news_letter/index.html.twig', [
            'templates' => $templates,
        ]);
    }
    /**
     * @Route("/new-template", name="create_new_template", methods={"POST"})
     */
    public function createNewTemplate(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $libelle=$request->get('libelle');
        $content=$request->get('content');
        $template=new NewsLetterTemplate();
        $template->setContent($content);
        $template->setLibelle($libelle);
        $template->setDateAjout(new \DateTime());
        $em->persist($template);
        $em->flush();
        return new JsonResponse(["id"=>$template->getId()]);
    }
    /**
     * @Route("/get-template/{id}", name="get_template", methods={"get"})
     */
    public function getTemplate($id)
    {
        $em = $this->getDoctrine()->getManager();
        $template=$em->getRepository(NewsLetterTemplate::class)->find((int)$id);
        if($template)
        {
            return new JsonResponse(["libelle"=>$template->getLibelle(),"content"=>$template->getContent()]);
        }
        return new JsonResponse(["error"=>"template not found"]);
    }
    /**
     * @Route("/delete-template/{id}", name="delete_template", methods={"get"})
     */
    public function deleteTemplate($id)
    {
        $em = $this->getDoctrine()->getManager();
        $template=$em->getRepository(NewsLetterTemplate::class)->find((int)$id);
        if($template)
        {
            $em->remove($template);
            $em->flush();
            return new JsonResponse(["message"=>"tempate supprimé avec succès"]);
        }
        return new JsonResponse(["error"=>"template not found"]);
    }
    /**
     * @Route("/envoyer-new/{id}", name="envoyer_new", methods={"post"})
     */
    public function envoyerNew($id,Request $request)
    {
        #set_time_limit(500);        
        $em = $this->getDoctrine()->getManager();
        $type=(int)$request->get('type');
        $template=$em->getRepository(NewsLetterTemplate::class)->find((int)$id);
        if($template)
        {
            //envoyer à une personne
            if($type==4)
            {
                $userId=(int)$request->get('userId');
                $user=$em->getRepository(User::class)->find($userId);
                if($user)
                {
                    $email1="togopapel@gmail.com";
                    $email="liye.yangala@galimatech.com";
                    #$this->createAnnonceMail([$user->getEmail()],$template->getLibelle(),$template->getContent());
                    #["libelle"=>$template->getLibelle(),"content"=>$template->getContent()];
                }
            }
            else
            {
                $array_email=[];
                $users=$em->getRepository(User::class)->findAll();
                #$users=[$users2[0],$users2[1]];
                $cpt=0;
                $sent=0;
                ;
                foreach ($users as $user)
                {
                    $role=$user->getRoles();
                    //envoyer au professionnels

                        if($type==2)
                        {
                            if(in_array("ROLE_PROFESSIONNEL",$role))
                            {
                                if($cpt<10)
                                {
                                    $array_email[] =$user->getEmail();
                                    $cpt++;
                                }
                                else
                                {
                                    #$this->createAnnonceMail($array_email,$template->getLibelle(),$template->getContent());
                                    $cpt=0;
                                    $array_email=[];

                                }
                                #$array_email[] =$user->getEmail();
                                # array_push($array_email,$user->getEmail());
                            }
                        }
                        //envoyer au particulier
                        if($type==3)
                        {
                            if(in_array("ROLE_PARTICULIER",$role))
                            {
                                
                                $array_email[] =$user->getEmail();
                                if($cpt==20)
                                {
                                    #var_dump($array_email);
                                    $cpt=0;
                                    $this->createAnnonceMail($array_email,$template->getLibelle(),$template->getContent());
                                    
                                    #return new JsonResponse(["error"=>"template not found4"]);
                                
                                    
                                    $array_email=[];
                                }
                                $cpt++;
                            }

                        }

                }
                #var_dump($array_email);
                #$this->createAnnonceMail($array_email,$template->getLibelle(),$template->getContent());

            }

        }

        return new JsonResponse(["error"=>"template not found"]);
    }

    /**
     * @Route("/get-user", name="search_user_for_mail")
     */
    public function getUserForMail(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $seachKeyWord=$request->get('key');
        $tabUser=[];
        if($seachKeyWord=="")
        {
            $users=$em->getRepository(User::class)->findAll();
            foreach ($users as $user)
            {
                if($user->getFirstname())
                {
                    array_push($tabUser,["user"=>$user->getName()." ".$user->getFirstname(),"id"=>$user->getId()]);
                }
            }
        }
        else
        {
            $sql ="SELECT id,name,firstname FROM `user` WHERE name LIKE '%".$seachKeyWord."%' or firstname LIKE '%".$seachKeyWord."%' ";
            $statement = $em->getConnection()->prepare($sql);
            $statement->execute();
            $result = $statement->fetchAll();
            foreach ($result as $res)
            {
                array_push($tabUser,["user"=>$res["name"]." ".$res["firstname"],"id"=>$res["id"]]);
            }
        }
        return new JsonResponse($tabUser);
    }
    /**
     * @Route("/edit-template", name="edit_template", methods={"post"})
     */
    public function editTemplate(Request $request)
    {
        $libelle=$request->get('libelle');
        $content=$request->get('content');
        $id=$request->get('id');
        $em = $this->getDoctrine()->getManager();
        $template=$em->getRepository(NewsLetterTemplate::class)->find((int)$id);
        if($template)
        {
            $template->setContent($content);
            $template->setLibelle($libelle);
            $em->persist($template);
            $em->flush();
            return new JsonResponse(["libelle"=>$template->getLibelle(),"content"=>$template->getContent(),"id"=>$template->getId()]);
        }
        return new JsonResponse(["error"=>"template not found"]);
    }

    public function createAnnonceMail(array $destEmail, string $objet,$content)
    {
        $this->tplmail($destEmail, $objet, $content);
    }
    /**
     * Template de mail par défaut
     *
     * @param string $destEmail
     * @param string $subject
     * @param string $path
     * @param array $options
     */
    private function tplmail(array $destEmail, string $subject, string $content)
    {
        $tmpDest=$destEmail[0];
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmail, 'togopapel'))
            ->to($tmpDest)
            ->subject($subject)
            ->html($content)
        ;
        if (count($destEmail)!=0)
        {
            for($i=1;$i<count($destEmail);$i++)
            {
                $email->addTo($destEmail[$i]);
            }
        }
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            echo $e->getMessage();
        }
    }
}
