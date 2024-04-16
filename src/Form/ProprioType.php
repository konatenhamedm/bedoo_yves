<?php

namespace App\Form;

use App\Entity\Proprio;
use App\Entity\Ville;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ProprioType extends AbstractType
{
    private $groupe;
    private $entreprise;
    public function __construct(Security $security){
        $this->groupe = $security->getUser()->getGroupe()->getCode();
        $this->entreprise = $security->getUser()->getEmploye()->getEntreprise();
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*->add('nomPrenoms')
            ->add('contacts')
            ->add('email')
            ->add('addresse')
            ->add('numCni')*/
            ->add('nomPrenoms', TextType::class, [
                'label' => 'Nom et Prénoms',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
           -> add('contacts', TextType::class, [
                'label' => 'Contact',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('addresse', TextType::class, [
                'label' => 'Adresse',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('addresse', EmailType::class, [
                'label' => 'Adresse',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('numCni', TextType::class, [
                'label' => 'N° de la CNI',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('Cni',     FichierType::class,
                [
                    /*  'label' => 'Fichier',*/
                    'label' => 'Cni',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true,
                    'validation_groups' => $options['validation_groups'],
                ])
            /*->add('nomPere')
            ->add('nomMere')
            ->add('whatsApp')*/

            ->add('nomPere', TextType::class, [
                'label' => 'Nom du père ',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('nomMere', TextType::class, [
                'label' => 'Non de la mère',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('whatsApp', TextType::class, [
                'label' => 'WhatsApp',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            
            
            ->add('dateNaiss',DateType::class,  [
                'attr' => ['class' => 'datepicker no-auto skip-init']
                , 'widget' => 'single_text'
                , 'format' => 'dd/MM/yyyy',
                'label'=>false
                , 'empty_data' => date('d/m/Y')
                , 'required' => false
                , 'html5' => false
            ])
          /*  ->add('lieuNaiss')
            ->add('prefession')*/
           
            ->add('lieuNaiss', TextType::class, [
                'label' => 'Lieu de Naissance',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('prefession', TextType::class, [
                'label' => 'Profession',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('dateCni',DateType::class,  [
                'attr' => ['class' => 'datepicker no-auto skip-init']
                , 'widget' => 'single_text'
                , 'format' => 'dd/MM/yyyy',
                'label'=>false
                , 'empty_data' => date('d/m/Y')
                , 'required' => false
                , 'html5' => false
            ])
          /*  ->add('nomPrenomsR')
            ->add('contactsR')
            ->add('emailR')
            ->add('adresseR')
            ->add('nomPrereR')
            ->add('nomMereR')
            ->add('whatsAppR')*/

            ->add('nomPrenomsR', TextType::class, [
                'label' => 'Nom et Prénoms',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('nomPrereR', TextType::class, [
                'label' => 'Nom du père',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('nomMereR', TextType::class, [
                'label' => 'Nom de la mère',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('contactsR', TextType::class, [
                'label' => 'Contact',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('whatsAppR', TextType::class, [
                'label' => 'WhatsApp',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            
            ->add('dateNaissR',DateType::class,  [
                'attr' => ['class' => 'datepicker no-auto skip-init']
                , 'widget' => 'single_text'
                , 'format' => 'dd/MM/yyyy',
                'label'=>false
                , 'empty_data' => date('d/m/Y')
                , 'required' => false
                , 'html5' => false
            ])
           // ->add('lieuNaissR')
            //->add('professionR')
            ->add('lieuNaissR', TextType::class, [
                'label' => 'Lieu de Naissance',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('professionR', TextType::class, [
                'label' => 'Profession',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('emailR', EmailType::class, [
                'label' => 'Email',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('adresseR', TextType::class, [
                'label' => 'Adresse',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
           
            ->add('dateCniR' ,DateType::class,  [
                'attr' => ['class' => 'datepicker no-auto skip-init']
                , 'widget' => 'single_text'
                , 'format' => 'dd/MM/yyyy',
                'label'=>'Etablir le'
                , 'empty_data' => date('d/m/Y')
                , 'required' => false
                , 'html5' => false
            ])
            ->add('numCniR', TextType::class, [
                'label' => 'N° de la pièce',
                //'attr' => ['placeholder' => 'Nom du chef de délégation']
            ])
            ->add('lien',     FichierType::class,
                [
                    /*  'label' => 'Fichier',*/
                    'label' => 'Lien social ou famillial',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true,
                    'validation_groups' => $options['validation_groups'],
                ])
            
            //->add('commission')
           // ->add('totalDu')
           // ->add('totalPaye')
           /* ->add('entreprise',EntityType::class, [
        'placeholder' => '----',
        'class' => Entreprise::class,
        'choice_label' => 'denomination',
        'label' => 'Entreprise',
        'attr' => ['class' => 'has-select2 form-select']
    ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Proprio::class,
            'doc_required' => true,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired(['validation_groups']);
    }
}
