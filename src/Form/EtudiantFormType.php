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

class EtudiantFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom',TextType::class,["attr"=>["class"=>"form-control"]])
            ->add('prenom',TextType::class,["attr"=>["class"=>"form-control"]])
            ->add('mailPerso',EmailType::class,["attr"=>["class"=>"form-control"]])
            ->add('email', EmailType::class,[
                'required' => false,
                'empty_data' => "",
                "attr"=>["class"=>"form-control"],
            ])
            ->add('filiere', EntityType::class,[
                'mapped' => false,
                'required' => false,
                'placeholder' => 'Filière',
                "attr"=>["class"=>"form-control"],
                'class' => Filiere::class,
                'choice_label' => function(Filiere $filiere){
                    return sprintf("%s %s %s", $filiere->getDiplome()->getNom(), $filiere->getNom(), $filiere->getAnnee());
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Utilisateur::class]);
    }
}