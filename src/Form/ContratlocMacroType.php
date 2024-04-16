<?php

namespace App\Form;

use App\Entity\Appartement;
use App\Entity\Contratloc;
use App\Entity\Fonction;
use App\Entity\Locataire;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContratlocMacroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           
            ->add('locataire',   EntityType::class, [
                'class' => Locataire::class,
                'choice_label' => 'nprenoms',
                'query_builder' => function (EntityRepository $er)  {
                    return $er->createQueryBuilder('f')
                        ->orderBy('f.id', 'ASC');
                },

                'label' => 'Locataire',
                'attr' => ['class' => 'has-select2 form-select commune']
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contratloc::class,
        ]);

        
    }
}
