<?php

namespace App\Form;

use App\Entity\Pasante;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; 
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class PasanteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, [
                'required' => true,
                ])
            ->add('apellido', TextType::class, [
                'required' => true,
                ])
            ->add('dni', TextType::class, [
                'required' => true,
                ])
            ->add('contacto', CollectionType::class, [
                'entry_type' => ContactoType::class,
                'entry_options' => ['label' => false],
                'by_reference' =>  false,
                'allow_add'=> true,
                'allow_delete'=> true,
                'label' => false,
            ])
            ->add('cuil', TextType::class, [
                'attr' => array('placeholder' => '        Cuil/Cuit 00-00000000-00'),
                'required' => true,
                ])
            ->add('legajo', TextType::class, [
                'required' => true,
                ])
                ->add('provincia', ChoiceType::class, [
                    'choices'  => [
                        'Seleccione una Provincia' => '',
                        'Santa Fe' => 'Santa Fe',
                        'Santiago del Estero' => 'Santiago del Estero',
                        'Córdoba' => 'Córdoba',
                        'Corrientes' => 'Corrientes',
                        'Entre Ríos' => 'Entre Ríos',
                        'Buenos Aires' => 'Buenos Aires',
                        'Catamarca' => 'Catamarca',
                        'Chaco' => 'Chaco',
                        'Chubut' => 'Chubut',
                        'Jujuy' => 'Jujuy',
                        'La Pampa' => 'La Pampa',
                        'Formosa' => 'Formosa',
                        'La Rioja' => 'La Rioja',
                        'Mendoza' => 'Mendoza',
                        'Misiones' => 'Misiones',
                        'Neuquén' => 'Neuquén',
                        'Río Negro' => 'Río Negro',
                        'Salta' => 'Salta',
                        'San Juan' => 'San Juan',
                        'San Luis' => 'San Luis',
                        'Santa Cruz' => 'Santa Cruz',
                        'Tierra del Fuego, Antártida e Islas del Atlántico Sur' => 'Tierra del Fuego, Antártida e Islas del Atlántico Sur',
                        'Tucumán' => 'Tucumán',
                    ],
                ])
                ->add('localidad')
                ->add('direccion')
            ->add('Guardar',SubmitType::class,['attr'=>['class'=>'btn btn-success']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pasante::class,
        ]);
    }
}
