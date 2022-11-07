<?php

namespace App\Form;

use App\Entity\Convenio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\AreaUnRaf;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; 
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class Paso3CargarDatosExpedienteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
        ->add('tituloExpediente',null,['required'   => true,])
        
        ->add('numeroExpediente',null,['required'   => true,])

        ->add('ultimaArea', EntityType::class,[
            'class'=> AreaUnRaf::class,
            'placeholder'=> 'Seleccione una Opcion',
                'choice_label'=>'nombre',
            /*'attr' => array('class' => 'form-control camposEstandar','required'=>true, 'placeholder' => 'ultimaArea')*/])

        ->add('areaSiguiente', EntityType::class,[
            'class'=> AreaUnRaf::class,
            'placeholder'=> 'Seleccione una Opcion',
            'choice_label'=>'nombre',])
        
        
        ->add('Siguiente',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Convenio::class,
        ]);
    }
}
