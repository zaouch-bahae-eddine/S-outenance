<?php

namespace App\Form;

use App\Entity\Prof;
use App\Entity\Soutenance;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class SoutenanceEvaluateurFormType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {

        $this->security = $security;
        $this->em = $em;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('evaluateurs', EntityType::class, [
                'class' => Prof::class,
                'multiple' => true,
                'required' => false,
                "attr"=>["class"=>"form-control"],
                'choices' => $this->em->getRepository(Prof::class)->findOtherProfs($this->security->getUser()->getProf()->getId()),
                'choice_label' => function(Prof $prof){
                        return sprintf('%s %s', $prof->getCompte()->getNom(), $prof->getCompte()->getPrenom());
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
