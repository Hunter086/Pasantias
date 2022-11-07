<?php

namespace App\Form;

use App\Entity\Pasantia;
use App\Entity\Pasante;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; 
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Doctrine\ORM\EntityRepository;

class Paso10CargarPasante extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            /**->add('pasante', EntityType::class, [
                'attr' => array('class' => 'form-control camposEstandar'),
                'class' => Pasante::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where("u.estadoPasante = 'Inactivo'")
                        ->orderBy('u.nombre', 'ASC');
                },
                'choice_label' => function (Pasante $customer) {
                    return $customer->getLegajo() . ' ' . $customer->getNombre(). ' ' . $customer->getApellido();},
                'expanded' => false,
                'multiple' => true,
            ])*/
        
        ->add('Finalizar',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pasantia::class,
        ]);
    }
}