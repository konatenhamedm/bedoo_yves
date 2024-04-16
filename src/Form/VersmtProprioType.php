<?php

namespace App\Form;

use App\Entity\TypeVersements;
use App\Entity\VersmtProprio;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VersmtProprioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*             ->add('libelle') */
            ->add('dateVersement', DateType::class,  [
                'attr' => ['class' => 'datepicker no-auto skip-init'],
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => 'Date',
                'empty_data' => date('d/m/Y'),
                'required' => false,
                'html5' => false
            ])
            ->add('type_versement', EntityType::class, [
                'class' => TypeVersements::class,
                'choice_label' => 'LibType',
                'label' => 'Type versement',
                'choice_attr' => function (TypeVersements $annee) {
                    return ['data-value' => $annee->getCodTyp()];
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e');
                },
                'attr' => ['class' => 'has-select2 form-select type']
            ])
            ->add('montant', TextType::class, ['attr' => ['class' => 'input-money input-mnt']])
            ->add('numero', TextType::class, ['attr' => ['class' => 'numero']]);
            /*  ->add('Proprio') */;
        /* ->add('locataire'); */
        $builder->get('montant')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VersmtProprio::class,
        ]);
    }
}
