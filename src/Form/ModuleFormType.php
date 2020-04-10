<?php


namespace App\Form;


use App\Entity\Filiere;
use App\Entity\Module;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModuleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('nom', TextType::class, ["attr"=>["class"=>"form-control"]])
            ->add('filiere', EntityType::class, [
                'class' => Filiere::class,
                'choice_label' => function(Filiere $filiere){
                    return sprintf('%s %s %s', $filiere->getDiplome()->getNom(), $filiere->getAnnee(), $filiere->getNom());
                },
                'placeholder' => 'Filière',
                "attr"=>["class"=>"form-control"],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Module::class,
        ]);
    }


}