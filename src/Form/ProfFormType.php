<?php

namespace App\Form;

use App\Entity\Filiere;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom',TextType::class)
            ->add('prenom',TextType::class)
            ->add('mailPerso',EmailType::class)
            ->add('email', EmailType::class,[
                'required' => false,
                'empty_data' => ""
            ])
            ->add('filiere', EntityType::class,[
                'mapped' => false,
                'placeholder' => 'Filière',
                'class' => Filiere::class,
                'choice_label' => function(Filiere $filiere){
                    return sprintf("%s %s %s", $filiere->getDiplome()->getNom(), $filiere->getNom(), $filiere->getAnnee());
                },
                'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Utilisateur::class]);
    }
}
