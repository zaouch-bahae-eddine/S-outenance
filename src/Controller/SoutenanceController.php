<?php

namespace App\Controller;

use App\Entity\Soutenance;
use App\Form\SoutenanceBaseFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/prof")
 */
class SoutenanceController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * FiliereController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/soutenance", name="soutenance_show")
     *  @Route("/soutenance/set/{id}", name="soutenance_set")
     */
    public function showSoutenanceAction(Soutenance $soutenance = null)
    {
        $formVide = $this->createForm(SoutenanceBaseFormType::class);
        $form = $this->createForm(SoutenanceBaseFormType::class,$soutenance);
        $repository  = $this->em->getRepository(Soutenance::class);
        $soutenances = $repository->findAll();
        if(!$soutenance)
            return $this->render('soutenance/showSoutenance.html.twig', [
                'soutenances' => $soutenances,
                'soutenanceForm' => $formVide->createView(),
                'soutenanceFormSet' =>$form->createView(),
                'idSoutenance' => 0,
                'setModel' => false,
            ]);
        return $this->render('soutenance/showSoutenance.html.twig', [
            'soutenances' => $soutenances,
            'soutenanceForm' => $formVide->createView(),
            'soutenanceFormSet' =>$form->createView(),
            'idSoutenance' => $soutenance->getId(),
            'setModel' => true,
        ]);
    }
    /**
     * @Route("/soutenance/base/add", name="soutenance_add")
     * @Route("/soutenance/base/add/{id}", name="soutenance_add_set")
     */
    public function addSoutenanceAction(Request $request, Soutenance $soutenance = null)
    {
        $form = $this->createForm(SoutenanceBaseFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(!$soutenance){
                /**
                 * @var Soutenance $soutenance
                 */
                $soutenance = $form->getData();
                $soutenance->setProf($this->getUser()->getProf());
                $msg = "ajouté";
            }
            else{
                $soutenance->setNom($form->getData()->getNom())
                    ->setAlerte($form->getData()->getAlerte());
                $msg = "modifié";
            }
            if(!$soutenance->getNom())
                throw new NotFoundHttpException('Nom ne peut pas être null');

            $this->em->persist($soutenance);
            $this->em->flush();
            $this->addFlash('success', 'Soutenance '.$msg);
        }
        return $this->redirectToRoute('soutenance_show');
    }

}
