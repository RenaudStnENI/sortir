<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('mail', EmailType::class)
            //->add('admin')
            //->add('actif')
            ->add('password', RepeatedType::class,
                [
                    'type'=>PasswordType::class,
                    'invalid_message'=>'Le mot de passe doit correspondre',
                    'required'=>true,
                    'first_options'=>array('label'=>'Mot de passe'),
                    'second_options'=>array('label'=>'Confirmer mot de passe'),
                ])
            //->add('site')
            //->add('sorties')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
