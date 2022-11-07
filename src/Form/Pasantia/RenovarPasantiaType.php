<?php

namespace App\Form;

use App\Entity\Pasantia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; 

class RenovarPasantiaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('fechaFin', DateType::class, [
                /*'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'Fecha Renovación del Convenio'),*/
                'widget' => 'single_text'
            ])
            ->add('Actualizar',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pasantia::class,
        ]);
    }
}
