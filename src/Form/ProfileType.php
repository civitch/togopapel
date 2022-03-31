<?php

namespace App\Form;

use App\Entity\Profile;
use App\Entity\Rubrique;
use App\Entity\Ville;
use App\Entity\IndicatifPays;
use App\Repository\RubriqueRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ProfileType extends AbstractType
{

    private $security;
    private $em;
    private $ville;
    private $rubrique;

    public function __construct(
        Security $security,
        EntityManagerInterface $em,
        VilleRepository $villeRepository,
        RubriqueRepository $rubriqueRepository
    )
    {
        $this->security = $security;
        $this->em = $em;
        $this->ville = $villeRepository;
        $this->rubrique = $rubriqueRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility', ChoiceType::class, [
                'choices' => [
                    'Madame' => 'madame',
                    'Monsieur' => 'monsieur',
                ],
                'label' => 'Civilité:',
                'required' => false,
                'placeholder' => 'Civilité'
            ])
            ->add('firstname', TextType::class, [
                'required' => false,
                'label' => 'Prénom:',
                'attr' => ['placeholder' => 'Saisir votre prénom:']
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'label' => 'Confirmer votre mot de passe:',
                'attr' => ['placeholder' => 'Saisir votre mot de passe actuel']
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Email:',
                'attr' => ['placeholder' => 'Saisir votre adresse email de connexion']
            ])

            ->add('telIndicatif', EntityType::class, [
                'class' => IndicatifPays::class,
                'label' => 'Indicatif du pays:',
                'choice_label' => 'title',
                'required' => true,
                'placeholder' => 'Sélectionner un indicatif pour votre numéro',
            ])

            ->add('tel', TelType::class, [
                'required' => false,
                'label' => 'Téléphone:',
                'attr' => ['placeholder' => 'Saisir votre numéro de téléphone']
            ])
            ->add('adresse', TextType::class, ['required' => false, 'label' => 'Adresse:'])
            ->add('name', TextType::class, ['required' => false, 'label' => 'Nom:'])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'label' => 'Ville:',
                'choice_label' => 'title',
                'required' => false,
                'placeholder' => 'Sélectionner votre ville',
                'attr' => ['class' => 'js-select-single']
            ])
            ->add("description", TextareaType::class, [
                'required' => false,
                'label' => 'Description:',
                'attr' => ['rows' => 8]
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Modifier profil',
                'attr'  => ['class' => 'btn-authform']
            ])
        ;

        /**
         * Event pour hydrater le formulaire
         */
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
            // l'objet profile
            $data = $event->getData();


            $form = $event->getForm();
            $user = $this->security->getUser();
            if($user->hasRole('ROLE_PROFESSIONNEL')){
                $form
                    ->add('rubrique', EntityType::class, [
                        'class' => Rubrique::class,
                        'choice_label' => 'title',
                        'required' => false,
                        'label' => 'Catégorie:',
                        'placeholder' => 'Sélectionner votre catégorie'
                    ])
                    ->add('society', TextType::class, ['attr' => ['placeholder' => 'Saisir la société (*)'], 'required' => false])
                    ->add('siren', TextType::class, ['attr' => ['placeholder' => 'Saisir numéro de société (*)'], 'required' => false])
            ;
                /**
                 * @var Profile $data
                 */
                $data->setRubrique($this->getRubrique());
                $data->setSociety($user->getSociety());
                $data->setSiren($user->getSiren());
            }
            /**
             * @var Profile $data
             */
            $data->setName($user->getName());
            $data->setEmail($user->getEmail());
            $data->setFirstname($user->getFirstname());
            $data->setAdresse($user->getAdresse());
            $data->setTelIndicatif($user->getIndicatifPays());
            $data->setTel($user->getTel());
            $data->setSociety($user->getSociety());
            $data->setVille($this->getVille());
            $data->setCivility($user->getCivility());
            $data->setDescription($user->getDescription());
        });
    }


    private function getVille(): ?Ville
    {
        $id = $this->security->getUser()->getVille();
        if(!is_null($id)){
            return $this->ville->find($id);
        }
        return null;
    }


    private function getRubrique(): ?Rubrique
    {
        $id = $this->security->getUser()->getRubrique();
        if(!is_null($id)){
            return $this->rubrique->find($id);
        }
        return null;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
