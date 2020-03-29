<?php

namespace App\Controller;

use App\Entity\Soutenance;
use App\Form\SoutenanceBaseFormType;
use App\Form\SoutenanceEvaluateurFormType;
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
     *  @Route("/soutenance/set/{id}-{nb}", name="soutenance_set")
     */
    public function showSoutenanceAction(Soutenance $soutenance = null, $nb = 0)
    {
        $formVide = $this->createForm(SoutenanceBaseFormType::class);
        $form = $this->createForm(SoutenanceBaseFormType::class,$soutenance);
        $formEvaluateur = $this->createForm(SoutenanceEvaluateurFormType::class,$soutenance);
        $repository  = $this->em->getRepository(Soutenance::class);
        $soutenances = $repository->findAll();
        if(!$soutenance)
            return $this->render('soutenance/showSoutenance.html.twig', [
                'soutenances' => $soutenances,
                'soutenanceForm' => $formVide->createView(),
                'soutenanceFormSet' =>$form->createView(),
                'soutenanceEvaluateurFormSet' => $formEvaluateur->createView(),
                'idSoutenance' => 0,
                'setModel' => false,
            ]);
        return $this->render('soutenance/showSoutenance.html.twig', [
            'soutenances' => $soutenances,
            'soutenanceForm' => $formVide->createView(),
            'soutenanceFormSet' =>$form->createView(),
            'soutenanceEvaluateurFormSet' => $formEvaluateur->createView(),
            'idSoutenance' => $soutenance->getId(),
            'setModel' => true,
            'nb' => $nb,
        ]);
    }
    /**
     * @Route("/soutenance/add", name="soutenance_add")
     * @Route("/soutenance/add/{id}-{nb}", name="soutenance_add_set")
     */
    public function addSoutenanceAction(Request $request, Soutenance $soutenance = null, $nb = 0)
    {

        $form = null;
        if($nb == 0)
            $form = $this->createForm(SoutenanceBaseFormType::class);

        if($nb == 1)
            $form = $this->createForm(SoutenanceEvaluateurFormType::class);
        $form->handleRequest($request);
        if(($form->isSubmitted() && $form->isValid())){
            if(!$soutenance){
                /**
                 * @var Soutenance $soutenance
                 */
                $soutenance = $form->getData();
                $soutenance->setProf($this->getUser()->getProf());
                $msg = "ajouté";
            }
            else{
                if($nb == 0)
                    $soutenance->setNom($form->getData()->getNom())
                        ->setAlerte($form->getData()->getAlerte())
                        ->setModule($form->getData()->getModule());
                if($nb == 1){
                    $evaluateurs = $soutenance->getEvaluateurs();
                    foreach ($evaluateurs as $evaluateur){
                        $soutenance->removeEvaluateur($evaluateur);
                        dump($soutenance);
                    }
                    $nvEvaluateurs = $form->getData()->getEvaluateurs();
                    foreach ($nvEvaluateurs as $evaluateur){
                        $soutenance->addEvaluateur($evaluateur);
                    }
                }
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
