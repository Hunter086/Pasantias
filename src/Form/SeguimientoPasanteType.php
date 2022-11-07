<?php

namespace App\Form;

use App\Entity\Pasante;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; 
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SeguimientoPasanteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('isSeguimientodelMes',null,
        ['required'=>true,/*'attr' => array('class' => 'form-check-input camposEstandar', 'placeholder' => 'isSeguimientodelMes')*/])
        ->add('isCertificado',null,
        ['required'=>true,/*'attr' => array('class' => 'form-check-input camposEstandar', 'placeholder' => 'isSeguimientodelMes')*/])
        ->add('isInformeSeguimiento', ChoiceType::class, [
            'choices'  => [
                'Informe Final No Realizado' => 'No Realizado',
                'Informe Final  Realizado' => 'Realizado',
            ],
            'label'=> false,
        ])
        ->add('Actualizar',SubmitType::class)
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pasante::class,
        ]);
    }
}
