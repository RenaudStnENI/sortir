<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,['label'=>'Nom de la sortie : '])
            ->add('dateHeureDebut',DateTimeType::class,['label'=>'Date et heure de sortie : ','widget'=>'single_text','html5'=>true/*,'attr'=>['class'=>'js-datepicker']*/])
            ->add('duree', TimeType::class,['label'=>'Durée : ','widget'=>'single_text', 'html5'=>true])
            ->add('dateLimiteInscription', DateType::class,['label'=>'Date limite d\'inscription : ','widget'=>'single_text','html5'=>true/*,'attr'=>['class'=>'js-datepicker']*/])
            ->add('nbInscriptionsMax', IntegerType::class,['label'=>'Nombre de participant maximum : '])
            ->add('infosSortie', TextareaType::class,['label'=>'Description et infos : '])
            ->add('lieu',null,['label'=>'Lieu : ', 'choice_label'=>'nom'])
            ->add('ville',EntityType::class,['class'=>Ville::class,'mapped'=>false,'label'=>'Ville : ','choice_label'=>'nom'])
            ->add('publier', SubmitType::class,['label'=>'Publier la sortie'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
