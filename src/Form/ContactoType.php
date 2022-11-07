<?php

namespace App\Form;

use App\Entity\Contacto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; 
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ContactoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, [
                'attr' => array('placeholder' => 'Nombre')])
            ->add('apellido', TextType::class, [
                'attr' => array('placeholder' => 'Apellido')])
            ->add('telefono', TextType::class, [
                'attr' => array('placeholder' => 'Telefono')])
            ->add('email',EmailType::class, [
                'attr' => array('placeholder' => 'Email')])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contacto::class,
        ]);
    }
}
