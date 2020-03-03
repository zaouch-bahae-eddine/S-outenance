<?php

namespace App\Controller;

use App\Entity\Filiere;
use App\Form\FiliereFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class FiliereController extends AbstractController
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
     * @Route("/filiere", name="filiere_show")
     *  @Route("/filiere/set/{id}", name="filiere_set")
     */
    public function showFiliereAction(Filiere $filiere = null)
    {
        $formVide = $this->createForm(FiliereFormType::class);
        $form = $this->createForm(FiliereFormType::class,$filiere);
        $repository  = $this->em->getRepository(Filiere::class);
        $filieres = $repository->findAll();
        if(!$filiere)
            return $this->render('filiere/showFiliere.html.twig', [
                'filieres' => $filieres,
                'filiereForm' => $formVide->createView(),
                'filiereFormSet' =>$form->createView(),
                'idFiliere' => 0,
            ]);
            return $this->render('filiere/showFiliere.html.twig', [
                'filieres' => $filieres,
                'filiereForm' => $formVide->createView(),
                'filiereFormSet' =>$form->createView(),
                'idFiliere' => $filiere->getId(),
            ]);

    }
    /**
     * @Route("/filiere/add", name="filiere_add")
     * @Route("/filiere/add/{id}", name="filiere_add_set")
     */
    public function addFiliereAction(Request $request, Filiere $filiere = null)
    {
        $form = $this->createForm(FiliereFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(!$filiere){
                $filiere = $form->getData();
                $msg = "ajouté";
            }
            else{
                $filiere
                    ->setNom($form->getData()->getNom())
                    ->setDiplome($form->getData()->getDiplome())
                    ->setAnnee($form->getData()->getAnnee())
                ;
                $msg = "modifié";
            }
            if(!$filiere->getNom())
                throw new NotFoundHttpException('Nom ne peut pas être null');

            $this->em->persist($filiere);
            $this->em->flush();
            $this->addFlash('success', 'Filière '.$msg);
        }
        return $this->redirectToRoute('filiere_show');
    }

    /**
     * @Route("/filiere/remove/{id}", name="filiere_remove")
     */
    public function removeFiliereAction(Filiere $filiere)
    {
        if(!$filiere)
            throw new NotFoundHttpException('Auccune filière a supprimer');
        $this->em->remove($filiere);
        $this->em->flush();

        $this->addFlash('success', 'Filière supprimé');
        return $this->redirectToRoute('filiere_show');
    }
}
