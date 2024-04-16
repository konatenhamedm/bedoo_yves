<?php

namespace App\Form;

use App\Entity\Appartement;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppartementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('LibAppart', TextType::class, [

                'label' => false
            ])
            ->add('NbrePieces', TextType::class, [
                'label' => false,
                'attr' => ['class' => 'input-money']
            ])
            ->add('NumEtage', IntegerType::class, [
                'label' => false,
                'attr' => ['class' => 'numero']
            ])
            ->add('Loyer', TextType::class, [
                'label' => false,
                'attr' => ['class' => 'input-money']
            ])
            /* ->add('Caution')*/
            ->add('Details', TextType::class, [
                'label' => false,
                "empty_data" => 'RAS',
            ]);
        /*->add('Oqp')*/
        /*->add('maisson')*/
        $builder->get('Loyer')->addModelTransformer(new ThousandNumberTransformer());
        $builder->get('NbrePieces')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appartement::class,
        ]);
    }
}
