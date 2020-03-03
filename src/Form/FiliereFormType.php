<?php


namespace App\Form;


use App\Entity\Diplome;
use App\Entity\Filiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiliereFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $annees['1-er année'] = '1-er année';
        for($i = 2; $i<8; $i++)
            $annees[$i.'-éme année'] = $i.'-éme année';

        $builder
            ->add('nom',TextType::class, [
            'label' => 'Nom Filière'])
            ->add('diplome', EntityType::class, [
                'class' => Diplome::class,
                'choice_label' => 'nom',
                'placeholder' => 'Diplome',
            ])
            ->add('annee',ChoiceType::class,[
                'choices' => $annees,
                'placeholder' => 'Année',
            ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Filiere::class]);
    }

}