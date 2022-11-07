<?php

namespace App\Form;

use App\Entity\Convenio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; 

class FechaRenovacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fechaFin', DateType::class, [
                'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'Fecha Fin del Convenio'),
                'widget' => 'single_text'
            ])
            ->add('Actualizar',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Convenio::class,
        ]);
    }
}
