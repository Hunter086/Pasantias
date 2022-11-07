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



class PagoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('fechaPago', DateType::class, [
                /*'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'Fecha Pago'),*/
                'widget' => 'single_text'
            ])
            ->add('mesAbonado', ChoiceType::class, [
                    /*'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'Cargar Mes'),*/
                'choices'  => [
                    '' => '',
                    'Enero' => 'Enero',
                    'Febrero' => 'Febrero',
                    'Marzo' => 'Marzo',
                    'Abril' => 'Abril',
                    'Mayo' => 'Mayo',
                    'Junio' => 'Junio',
                    'Julio' => 'Julio',
                    'Agosto' => 'Agosto',
                    'Septiembre' => 'Septiembre',
                    'Octubre' => 'Octubre',
                    'Noviembre' => 'Noviembre',
                    'Diciembre' => 'Diciembre',
                ],
            ])
            ->add('porcentajedeCobro',null,[/*'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'Porcentaje de Cobro')*/])
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
