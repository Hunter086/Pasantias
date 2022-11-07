<?php

namespace App\Form;

use App\Entity\Pago;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType; 
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class ComprobantePagoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('totalAbonado', MoneyType::class, array(
            'scale' => 2,
            
            'label' => 'form.price',
            'attr' => array(
                'onfocusout'=>"totalCobro()",
                'class' => 'form-control camposEstandar'
                , 'placeholder' => 'Total Abonado',
                'min' => '0.00',
            ),
            'currency' => 'ARG'
        ))
        ->add('totalaCobrar', MoneyType::class, array(
            'scale' => 2,
            
            'label' => 'form.price',
            'attr' => array('invalid_message' => 'El número de elementos introducido no es válido.',
                'class' => 'form-control camposEstandar'
            , 'placeholder' => 'Total Cobrar',
            'min' => '0.00',
            ),
            'currency' => 'ARG'
        ))
        
        

        

        ->add('comprobantePago', FileType::class, [
            'attr' => array('class' => 'form-control camposEstandar'),
            'label' => 'Seleccione un Documento (PDF file)',

            // unmapped means that this field is not associated to any entity property
            'mapped' => false,

            // make it optional so you don't have to re-upload the PDF file
            // every time you edit the Product details
            'required' => true,

            // unmapped fields can't define their validation using annotations
            // in the associated entity, so you can use the PHP constraint classes
            'constraints' => [
                new File([
                    'maxSize' => '30720k',
                    'mimeTypes' => [
                        'application/pdf',
                        'application/x-pdf',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid PDF document',
                ])
            ],
        ])

        ->add('Guardar',SubmitType::class,[
            'attr' => array('class' => 'btn btn-outline-info btn-lg')
        ] ) 
        
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pago::class,
        ]);
    }
}
