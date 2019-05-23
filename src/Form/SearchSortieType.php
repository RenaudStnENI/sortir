<?php

namespace App\Form;

use App\Entity\Site;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rechercher',SubmitType::class)
            ->add('site',EntityType::class,[
                'class'=>Site::class,
                'choice_label'=>'nom',
                'placeholder' => 'Tous les sites',
                'required'=>false
            ])
            ->add('nom', TextType::class,['label'=>'Le nom de la sortie contient','required'=>false])

            ->add('dateMin',DateType::class,['label'=>'entre','widget'=>'single_text','html5'=>true,'required'=>false])
            ->add('dateMax',DateType::class,['label'=>'et','widget'=>'single_text','html5'=>true,'required'=>false])
            ->add('isOrganisateur',CheckboxType::class,['label'=>'Sorties dont je suis l\'organisateur/trice','required'=>false])
            ->add('isInscrit',CheckboxType::class,['label'=>'Sorties auxquelles je suis inscrit/e','required'=>false])
            ->add('isNotInscrit',CheckboxType::class,['label'=>'Sorties auxquelles je ne suis pas inscrit/e','required'=>false])
            ->add('sortiesPassees',CheckboxType::class,['label'=>'Sorties Passées','required'=>false])


        ;
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
