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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class Paso4CargarDatosdelConvenioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('fechaInicio', DateType::class, [
            /*'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'fechaInicio'),*/
            'widget' => 'single_text'
        ])
        ->add('fechaFin', DateType::class, [
            /*'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'fechaFin'),*/
            'widget' => 'single_text'
        ])
        ->add('isRenovacionAutomatica', ChoiceType::class, [
            'choices'  => [
                'Tipo De Renovación' => '',
                'Convenio con renovación Automática' => 'AUTOMATICO',
                'Convenio sin renovación Automática' => 'MANUAL'
            ],
        ])
        ->add('documentoConvenio', FileType::class, [
            'label' => 'Seleccione un Documento (PDF)',
            'attr' => array("accept"=>".pdf"),
            // unmapped means that this field is not associated to any entity property
            'mapped' => false,

            // make it optional so you don't have to re-upload the PDF file
            // every time you edit the Product details
            'required' => true,

            // unmapped fields can't define their validation using annotations
            // in the associated entity, so you can use the PHP constraint classes
            'constraints' => [
                new File([
                    'maxSize' => '8M',
                    'mimeTypes' => [
                        'application/pdf',
                        'application/x-pdf',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid PDF document',
                ])
            ],
        ])
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
