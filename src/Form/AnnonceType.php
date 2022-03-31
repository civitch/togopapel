<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserPack;
use App\Entity\Ville;
use App\Services\Entity\UserPackEntity;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Annonce;
use App\Entity\Categorie;
use App\Entity\Rubrique;
use App\Entity\IndicatifPays;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Security;


class AnnonceType extends AbstractType
{
    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $info = $this->getGroupes();
        $builder
            ->add('title', TextType::class)
            ->add('type', ChoiceType::class, [
                'choices'  => self::getChoicesType(),
                'expanded' => true,
                'label'    => false
            ])
            ->add('price', MoneyType::class, [
                'required' => false,
                'currency' => 'XOF',
                'help'     => "Ne rien mettre entre les chiffres (exemple: 1000000)"
            ])
            ->add('state', ChoiceType::class, [
                'choices' => $this->getChoicesState(),
                'placeholder' => 'Sélectionner un état',
                'required' => true
            ])
            ->add('description', TextareaType::class, ['required' => false, 'attr' => ['rows' => 10]])
            ->add('categorie', EntityType::class, [
                'class'        => Categorie::class,
                'choice_label' => 'title',
                'required'     => true,
                'placeholder'  => 'Sélectionner une catégorie',
                'group_by'     => function($choice) use ($info) {
                    /** @var Categorie $choice */
                    /** @var Rubrique $info */
                    $tab = $this->est($info);
                    if(in_array($choice->getRubrique()->getId(), array_keys($tab)))
                    {
                        return $tab[$choice->getRubrique()->getId()];
                    }
                    else{
                        return null;
                    }
                },
                'attr' => ['class' => 'js-select-single']
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'placeholder' => 'Sélectionner une ville',
                'choice_label' => 'title',
                'required' => true,
                'query_builder' => function(EntityRepository $em) {
                    return $em->createQueryBuilder('v')
                        ->orderBy('v.title', 'ASC')
                        ;
                },
                'attr' => ['class' => 'js-select-single']
            ])
            ->add('adresse')
            ->add('lat', HiddenType::class)
            ->add('lng', HiddenType::class)
            ->add('pictureFileOne', FileType::class, ['label' => false, 'required' => false])
            ->add('pictureFileTwo', FileType::class, ['label' => false, 'required' => false])
            ->add('pictureFileThree', FileType::class, ['label' => false, 'required' => false])
            ->add('pictureFileFour', FileType::class, ['label' => false, 'required' => false])
            ->add('hideTel', CheckboxType::class, ["required" => false, 'label' => "Afficher le numéro de téléphone"])
            ->add('save', SubmitType::class, ['label' => 'Publier','attr' => ['class' => 'btn-authform']])
        ;

        // Evènement POST_SET_DATA qui récupère la valeur du state de façon globale sur le formulaire
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $annonce = $event->getData();
            if($annonce->getState()){
                $state = $annonce->getStateForm($annonce->getState());
                $event->getForm()->get('state')->setData($state);
            }
        });

        // Evènement PRE_SET_DATA pour récupérer les informations concernant Packs utilisateurs
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event){
            $annonce = $event->getData();
            $form = $event->getForm();
            $this->getPacksAvailable($form);

            // Vérifier que le current_user a un nom, prénom, tel définit
            $user = $this->security->getUser();
            /** @var User $user */
            if($user->getTel()){
                $this->getAccountInformation($form, $user->getTel(), true);
            }else{
                $this->getAccountInformation($form, $user->getTel(), false);
            }

            if($user->getName())
            {
                $this->getAccountInformation($form, $user->getName(), true, 'name');
            }
            else{
                $this->getAccountInformation($form, $user->getName(), false, 'name');
            }

            if($user->getFirstname())
            {
                $this->getAccountInformation($form, $user->getFirstname(), true, 'firstname');
            }
            else{
                $this->getAccountInformation($form, $user->getFirstname(), false, 'firstname');
            }
            if($user->getIndicatifPays() && $user->getIndicatifPays()->getId()!=0){
                dump($user->getIndicatifPays()->getTitle());
                $this->getAccountInformation($form, $user->getIndicatifPays(), true, 'indicatifPays');
            }
            else{
                $this->getAccountInformation($form, $user->getIndicatifPays(), false, 'indicatifPays');
            }
        });
    }

    /**
     * Liste les packs pour les utilisateurs
     *
     * @param FormInterface $form
     */
    private function getPacksAvailable(FormInterface $form)
    {
        $user = $this->security->getUser();
        /** @var User $user */
        $currentPacks = $user->getUserPacks();
        if(!$currentPacks->isEmpty()){
            foreach ($currentPacks as $userPack)
            {
                /** @var UserPack $userPack */
                switch ($userPack->getPack()->getRole()){
                    case UserPackEntity::STAR:
                        $form->add('packStar', CheckboxType::class, ['required' => false]);
                        break;
                    case UserPackEntity::VIP:
                        $form->add('packVip', CheckboxType::class, ['required' => false]);
                        break;
                    case UserPackEntity::PREMIUM:
                        $form->add('packPremium', CheckboxType::class, ['required' => false]);
                        break;
                }
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }

    private function getGroupes()
    {
        return $this->em->getRepository(Rubrique::class)->findAll();
    }

    private function est($info)
    {
        $tab = [];
        foreach ($info as $item)
        {
            $tab[$item->getId()] = $item->getTitle();
        }
        return $tab;
    }

    public static function getChoicesType()
    {
        $choices = Annonce::TYPE;
        $output = [];
        foreach($choices as $k => $v) {
            $output[$v] = $k;
        }
        return $output;
    }

    private function getChoicesState()
    {
        $choices = Annonce::STATE;
        $output = [];
        foreach($choices as $k => $v) {
            $output[$v] = $k;
        }
        return $output;
    }

    private function getAccountInformation(FormInterface $form, $data, $disable = false, string  $field = 'tel')
    {
        if($field === 'tel'){
            if($disable){
                $form
                    ->add('tel', TextType::class, [
                        'mapped'   => false,
                        'disabled' => $disable,
                        'data'     => $data
                    ]);
            }else{
                $form
                    ->add('tel', TelType::class, [
                        'mapped'   => false,
                        'disabled' => $disable
                    ]);
            }
        }else if($field === 'indicatifPays'){

            $form->add('indicatifPays', EntityType::class, [
                'class' => IndicatifPays::class,
                'label' => 'Indicatif du pays:',
                'choice_label' => 'title',
                'disabled' => $disable,
                'mapped'   => false,
                'data' => $data,
                'placeholder' => 'Sélectionner un indicatif pour votre numéro',
            ]);

        } else{
            if($disable){
                $form->add($field, TextType::class, [
                    'mapped'   => false,
                    'disabled' => $disable,
                    'data'     => $data
                ]);
            }else{
                $form->add($field, TextType::class, [
                    'mapped'   => false,
                    'disabled' => false
                ]);
            }

        }
    }

}
