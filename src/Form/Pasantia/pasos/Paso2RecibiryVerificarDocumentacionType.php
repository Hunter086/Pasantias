<?php

namespace App\Form;

use App\Entity\Pasantia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; 
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\AreaUnRaf;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class Paso2RecibiryVerificarDocumentacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('areaActual', EntityType::class,[
            'class'=> AreaUnRaf::class,
            'placeholder'=> 'Seleccione una Opcion',
            'choice_label'=>'nombre'
            #'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'Area Encargada')
        ] )
        
        ->add('areaEncargada', EntityType::class,[
            'class'=> AreaUnRaf::class,
            'placeholder'=> 'Seleccione una Opcion',
            'choice_label'=>'nombre'
            #'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'Area Siguiente')
        ] )
        ->add('Siguiente',SubmitType::class,[
            #'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'Area Siguiente')
        ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pasantia::class,
        ]);
    }
}
