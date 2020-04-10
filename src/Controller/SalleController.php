<?php

namespace App\Controller;

use App\Entity\Salle;
use App\Form\SalleFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class SalleController extends AbstractController
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * FiliereController constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/salle", name="salle_show")
     *  @Route("/salle/set/{id}", name="salle_set")
     */
    public function showSalleAction(Salle $salle = null)
    {
        $formVide = $this->createForm(SalleFormType::class);
        $form = $this->createForm(SalleFormType::class,$salle);
        $repository  = $this->em->getRepository(Salle::class);
        $salles = $repository->findAll();
        if(!$salle)
            return $this->render('salle/showSalle.html.twig', [
                'salles' => $salles,
                'salleForm' => $formVide->createView(),
                'salleFormSet' =>$form->createView(),
                'idSalle' => 0,
                'setModel' => false
            ]);
        return $this->render('salle/showSalle.html.twig', [
            'salles' => $salles,
            'salleForm' => $formVide->createView(),
            'salleFormSet' =>$form->createView(),
            'idSalle' => $salle->getId(),
            'setModel' => true
        ]);

    }
    /**
     * @Route("/salle/add", name="salle_add")
     * @Route("/salle/add/{id}", name="salle_add_set")
     */
    public function addSalleAction(Request $request, Salle $salle = null)
    {
        $form = $this->createForm(SalleFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(!$salle){
                /**
                 * @var Salle $salle
                 */
                $salle = $form->getData();
                $msg = "ajouté";
            }
            else{
                $salle->setNom($form->getData()->getNom())
                ->setEtage($form->getData()->getEtage())
                ->setCapacite($form->getData()->getCapacite());
                $msg = "modifié";
            }
            if(!$salle->getNom())
                throw new NotFoundHttpException('Nom ne peut pas être null');

            $this->em->persist($salle);
            $this->em->flush();
            $this->addFlash('success', 'Salle '.$msg);
        }
        return $this->redirectToRoute('salle_show');
    }

    /**
     * @Route("/salle/remove/{id}", name="salle_remove")
     */
    public function removeSalleAction(Salle $salle)
    {
        if(!$salle)
            throw new NotFoundHttpException('Auccune Salle a supprimer');
        $this->em->remove($salle);
        $this->em->flush();

        $this->addFlash('success', 'Salle supprimé !');
        return $this->redirectToRoute('salle_show');
    }
}
