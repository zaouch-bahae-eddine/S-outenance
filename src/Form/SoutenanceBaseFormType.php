<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\Prof;
use App\Entity\Soutenance;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class SoutenanceBaseFormType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {

        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Prof $prof */
        $prof = $this->security->getUser()->getProf();
        $filieresProf = $prof->getFilieres();
        $modulesProf = [];
        $i = 0;
        foreach ($filieresProf as $filiere){
            $modules = $filiere->getModules();
            foreach ($modules as $module){
                $modulesProf[$i] = $module;
                $i++;
            }
        }
        $builder
            ->add('nom')
            ->add('alerte', DateTimeType::class)
            ->add('module', EntityType::class, [
                'class' => Module::class,
                'choices' => $modulesProf,
                'choice_label' => function (Module $module){
                    return sprintf('Module: %s, Filiére: %s, Année: %s', $module->getNom(), $module->getFiliere()->getNom(), $module->getFiliere()->getAnnee());
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Soutenance::class,
        ]);
    }
}
