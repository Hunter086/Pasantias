<?php

namespace App\Form;

use App\Entity\Pasantia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Convenio;
use App\Entity\AreaUnRaf;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType; 
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class Paso6CargarConvenioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('areaActual', EntityType::class,[
            'class'=> AreaUnRaf::class,
            'placeholder'=> 'Seleccione una Opcion',
            'choice_label'=>'nombre',
            #'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'UltimaArea')
        ] )
        
       
        ->add('convenio', EntityType::class,[
            
            'class'=> Convenio::class,
            'query_builder'=>function (EntityRepository $er){
                return $er->createQueryBuilder('cp')
                ->add('where',"cp.estado= 'Activo'");
            },
            'choice_label'=>'empresa.nombre',
            'placeholder'=> 'Seleccione una Opcion'
            #'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'Convenio')
        ] )
        
        ->add('areaEncargada', EntityType::class,[
            'class'=> AreaUnRaf::class,
            'placeholder'=> 'Seleccione una Opcion',
            'choice_label'=>'nombre',
            #'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'UltimaArea')
        ] )
        
        
        ->add('Siguiente',SubmitType::class)
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pasantia::class,
        ]);
    }
}
