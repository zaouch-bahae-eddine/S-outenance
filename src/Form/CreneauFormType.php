<?php

namespace App\Form;

use App\Entity\Creneau;
use App\Entity\Salle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreneauFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('capacite',null,["attr"=>["class"=>"form-control"]])
            ->add('date',null,["attr"=>["class"=>"form-control"]])
            ->add('heure',null,["attr"=>["class"=>"form-control"]])
            ->add('duree',null,["attr"=>["class"=>"form-control"]])
            ->add('salles', EntityType::class,[
                'class' => Salle::class,
                'multiple' => true,
                'required' => false,
                'choice_label' => function(Salle $salle){
                    return sprintf('Salle %s, etage %s', $salle->getNom(), $salle->getEtage());
                },
                "attr"=>["class"=>"form-control"],
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Creneau::class]);
    }
}
