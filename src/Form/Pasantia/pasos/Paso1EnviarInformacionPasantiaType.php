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

class Paso1EnviarInformacionPasantiaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('areaActual', EntityType::class,[
                'class'=> AreaUnRaf::class,
                'placeholder'=> 'Seleccione una Opcion',
                'choice_label'=>'nombre',
                /*'attr' => array('id'=>'filter-menu', 'data-native-menu'=>'false')*/
            ] )
            ->add('nombre', TextType::class, [
                /*'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'Nombre Pasantía')*/
                ])
            ->add('areaEncargada', EntityType::class,[
                'class'=> AreaUnRaf::class,
                'placeholder'=> 'Seleccione una Opcion',
                'choice_label'=>'nombre',
                /*'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'Area Siguiente')*/
            ] )
            ->add('Iniciar',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pasantia::class,
            'attr' => ['id' => 'form']
        ]);
    }
}
