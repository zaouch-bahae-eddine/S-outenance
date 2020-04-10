<?php


namespace App\Form;


use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',null,["attr"=>["class"=>"form-control"]])
            ->add('prenom', null,["attr"=>["class"=>"form-control"]])
            ->add('email',null,["attr"=>["class"=>"form-control"]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Utilisateur::class]);
    }

}