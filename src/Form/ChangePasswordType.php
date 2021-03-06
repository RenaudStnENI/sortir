<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, array(
                'mapped' => false, 'label'=>'Ancien mot de passe'))
            ->add('password', RepeatedType::class,
                [
                    'mapped' => false,
                    'type'=>PasswordType::class,
                    'invalid_message'=>'Le mot de passe doit correspondre',
                    'required'=>false,
                    'first_options'=>array('label'=>'Nouveau mot de passe'),
                    'second_options'=>array('label'=>'Confirmer mot de passe'),
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
