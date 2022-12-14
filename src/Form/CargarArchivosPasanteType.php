<?php

namespace App\Form;

use App\Entity\Archivos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; 

class CargarArchivosPasanteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            
            ->add('archivos', FileType::class, array(
                'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'Archivos',
                'multiple' => 'multiple'),
                
            ))
            
           
            
            ->add('Guardar',SubmitType::class)
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        
        ]);
    }
}
