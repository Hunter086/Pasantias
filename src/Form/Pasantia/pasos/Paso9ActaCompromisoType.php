<?php

namespace App\Form;

use App\Entity\Pasantia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use App\Entity\AreaUnRaf;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class Paso9ActaCompromisoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('areaActual', EntityType::class,[
            'class'=> AreaUnRaf::class,
            'placeholder'=> 'Seleccione una Opcion',
            'choice_label'=>'nombre',
            #'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'UltimaArea')
        ] )
        ->add('fechaInicio', DateType::class, [
            'required'=>false,
            #'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'Fecha Inicio Pasantia'),
            'widget' => 'single_text'
        ])
        ->add('fechaFin', DateType::class, [
            'required'=>false,
            #'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'Fecha Fin Pasantia'),
            'widget' => 'single_text'
        ])
        ->add('areaEncargada', EntityType::class,[
            
            'class'=> AreaUnRaf::class,
            'placeholder'=> 'Seleccione una Opcion',
            'choice_label'=>'nombre',
            #'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'areaEncargada')
        ] )


        ->add('Siguiente',SubmitType::class)
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pasantia::class,
        ]);
    }
}
