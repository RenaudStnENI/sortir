<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AddUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('photo')
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('username')
            ->add('mail', EmailType::class,
                [
                    'invalid_message'=>'E-mail non valide' ,
                    'required'=>true,
                ])
            //->add('admin')
            //->add('actif')
            ->add('password', RepeatedType::class,
                [
                    'type'=>PasswordType::class,
                    'invalid_message'=>'Le mot de passe doit correspondre',
                    'required'=>false,
                    'first_options'=>array('label'=>'Mot de passe'),
                    'second_options'=>array('label'=>'Confirmer mot de passe'),
                ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
