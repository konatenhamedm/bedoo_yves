<?php

namespace App\Form;

use App\Entity\Appartement;
use App\Entity\CampagneContrat;
use App\Entity\Locataire;
use App\Entity\Maison;
use App\Entity\Proprio;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampagneContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('proprietaire', EntityType::class, [
                'class' => Proprio::class,
                'choice_label' => 'nomPrenoms',
                'label' => false,
                'attr' => ['class' => '']
            ])
            ->add('locataire', EntityType::class, [
                'class' => Locataire::class,
                'choice_label' => 'NPrenoms',
                'label' => false,
                'attr' => ['class' => '']
            ])
            ->add('maison', EntityType::class, [
                'class' => Maison::class,
                'choice_label' => 'LibMaison',
                'label' => false,
                'attr' => ['class' => '']
            ])
            ->add('loyer', TextType::class, ['label' => false, 'attr' => ['class' => 'input-money input-mnt']])

            ->add('numAppartement', EntityType::class, [
                'class' => Appartement::class,
                'choice_label' => 'LibAppart',
                'label' => false,
                'attr' => ['class' => '']
            ])->add('dateLimite', DateType::class,  [
                'label' => false,
                'attr' => ['class' => 'datepicker no-auto skip-init'], 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'empty_data' => date('d/m/Y'), 'required' => false, 'html5' => false
            ])
            /*->add('locataire_hide',HiddenType::class,[
                'label' => false,
                'mapped'=>false
            ])*/

            /*->add('dateLimite')*/;
        $builder->get('loyer')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CampagneContrat::class,
        ]);
    }
}
