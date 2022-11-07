<?php

namespace App\Form;

use App\Entity\Empresa;
use App\Entity\Contacto;
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

class EmpresaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('razonSocial')
            ->add('nombre')
            ->add('cuit')
            ->add('contacto', CollectionType::class, [
                'entry_type' => ContactoType::class,
                'entry_options' => ['label' => false],
                'by_reference' =>  false,
                'allow_add'=> true,
                'allow_delete'=> true,
                'label' => false,
            ])
            ->add('situaciondeIva', ChoiceType::class, [
                'choices'  => [
                    'Situación del Iva' => '',
                    'IVA Responsable Inscripto' => 'IVA Responsable Inscripto',
                    'IVA Responsable no Inscripto' => 'IVA Responsable no Inscripto',
                    'IVA no Responsable' => 'IVA no Responsable',
                    'IVA Sujeto Exento' => 'IVA Sujeto Exento',
                    'Consumidor Final' => 'Consumidor Final',
                    'Responsable Monotributo' => 'Responsable Monotributo',
                    'Sujeto no Categorizado' => 'Sujeto no Categorizado',
                    'Proveedor del Exterior' => 'Proveedor del Exterior',
                    'Cliente del Exterior' => 'Cliente del Exterior',
                    ' IVA Liberado – Ley Nº 19.640' => ' IVA Liberado – Ley Nº 19.640',
                    'IVA Responsable Inscripto – Agente de Percepción' => 'IVA Responsable Inscripto – Agente de Percepción',
                    'Pequeño Contribuyente Eventual' => 'Pequeño Contribuyente Eventual',
                    'Monotributista Social' => 'Monotributista Social',
                    'Pequeño Contribuyente Eventual Social' => 'Pequeño Contribuyente Eventual Social',
                ],
            ])
            ->add('web')
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
                    'Ciudad Autónoma de Buenos Aires' => 'Ciudad Autónoma de Buenos Aires',
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
            'data_class' => Empresa::class,
        ]);
    }
}
