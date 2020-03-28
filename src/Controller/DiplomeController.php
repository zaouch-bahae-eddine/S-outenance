<?php

namespace App\Controller;

use App\Entity\Diplome;
use App\Entity\Filiere;
use App\Form\DiplomeFormType;
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
class DiplomeController extends AbstractController
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
     * @Route("/diplome", name="diplome_show")
     * @Route("/diplome/set/{id}", name="diplome_set")
     */
    public function showDiplomeAction(Diplome $diplome = null)
    {
        $formVide = $this->createForm(DiplomeFormType::class);
        $form = $this->createForm(DiplomeFormType::class,$diplome);
        $repository  = $this->em->getRepository(Diplome::class);
        $diplomes = $repository->findAll();
        if(!$diplome)
            return $this->render('diplome/showDiplome.html.twig', [
                'diplomes' => $diplomes,
                'diplomeForm' => $formVide->createView(),
                'diplomeFormSet' =>$form->createView(),
                'idDiplome' => 0,
                'setModel' => false
            ]);
        return $this->render('diplome/showDiplome.html.twig', [
            'diplomes' => $diplomes,
            'diplomeForm' => $formVide->createView(),
            'diplomeFormSet' =>$form->createView(),
            'idDiplome' => $diplome->getId(),
            'setModel' => true
        ]);

    }
    /**
     * @Route("/diplome/add", name="diplome_add")
     * @Route("/diplome/add/{id}", name="diplome_add_set")
     */
    public function addDiplomeAction(Request $request, Diplome $diplome = null)
    {
        $form = $this->createForm(DiplomeFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(!$diplome){
                /**
                 * @var Diplome $diplome
                 */
                $diplome = $form->getData();
                $diplome->setAdmin($this->getUser()->getAdmin());
                $msg = "ajouté";
            }
            else{
                $diplome->setNom($form->getData()->getNom());
                $msg = "modifié";
            }
            if(!$diplome->getNom())
                throw new NotFoundHttpException('Nom ne peut pas être null');

            $this->em->persist($diplome);
            $this->em->flush();
            $this->addFlash('success', 'Diplome '.$msg);
        }
        return $this->redirectToRoute('diplome_show');
    }

    /**
     * @Route("/diplome/remove/{id}", name="diplome_remove")
     */
    public function removeDiplomeAction(Diplome $diplome)
    {
        if(!$diplome)
            throw new NotFoundHttpException('Auccun diplome a supprimer');
        $this->em->remove($diplome);
        $this->em->flush();

        $this->addFlash('success', 'Diplome supprimé');
        return $this->redirectToRoute('diplome_show');
    }
}
