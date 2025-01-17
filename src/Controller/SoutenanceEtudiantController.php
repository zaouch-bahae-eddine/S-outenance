<?php

namespace App\Controller;

use App\Entity\Creneau;
use App\Entity\Note;
use App\Entity\Rendu;
use App\Entity\Soutenance;
use App\Form\RenduFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SoutenanceEtudiantController
 * @package App\Controller
 * @Route("/etudiant")
 */
class SoutenanceEtudiantController extends AbstractController
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
     * @Route("/soutenance", name="soutenance_etudiant_show")
     * @Route("/soutenance/{id}/rendu/set", name="rendu_set")
     */
    public function showSoutenanceEtudiant(Soutenance $soutenance = null)
    {
        $form = $this->createForm(RenduFormType::class);

        $etudiant = $this->getUser()->getEtudiant();
        $rep = $this->em->getRepository(Soutenance::class);
        $repRendu = $this->em->getRepository(Rendu::class);
        $soutenanceShow = null;
        $notesSoutenance = null;
        $rendusBySoutenance = null;
        $soutenancesByModule = null;
        foreach ($etudiant->getFiliere()->getModules() as $module){
            $soutenancesCourantes = $rep->findSoutenancesByModule($module->getId());
            foreach ($soutenancesCourantes as $soutenaceByModule){
                $canShow = true;
                $soutenanceShow[$soutenaceByModule->getId()]["bool"]= $canShow;
                $soutenanceShow[$soutenaceByModule->getId()]["creneau"]= null;
                if(!$canShow)
                    break;
                foreach ($etudiant->getCreneaus() as $creneau)
                    if($soutenaceByModule->getId() == $creneau->getSoutenance()->getId()){
                        $canShow = false;
                        $soutenanceShow[$soutenaceByModule->getId()]["bool"]= false;
                        $soutenanceShow[$soutenaceByModule->getId()]["creneau"] = $creneau;
                        break;
                    }
            }
            if($soutenancesCourantes != null)
                $soutenancesByModule[$module->getId()] = $soutenancesCourantes;
            foreach ($soutenancesCourantes as $sou){
                $notesSoutenance[$sou->getId()] = $this->em->getRepository(Note::class)->findBy(['soutenance' => $sou, 'etudiant' => $this->getUser()->getEtudiant()]);
                $rendusBySoutenance[$sou->getId()] = $repRendu->findRenduBySoutenanceAndEtudiant($this->getUser()->getEtudiant(), $sou->getId());
            }
        }
        if($soutenance){
            $mesRendus = $repRendu->findRenduBySoutenanceAndEtudiant($this->getUser()->getEtudiant(), $soutenance->getId());
            return $this->render('soutenance_etudiant/showSoutenanceEtudiant.html.twig', [
                'soutenancesByModule' => $soutenancesByModule ,
                'modules' => $etudiant->getFiliere()->getModules(),
                'canShow' => $soutenanceShow,
                "form"=>$form->createView(),
                'setModel' => true,
                'soutenance' => $soutenance,
                'mesRendus' => $mesRendus,
                'notesSoutenance' => $notesSoutenance,
                'rendusBySoutenance' => $rendusBySoutenance,
            ]);
        }else{
            return $this->render('soutenance_etudiant/showSoutenanceEtudiant.html.twig', [
                'soutenancesByModule' => $soutenancesByModule ,
                'modules' => $etudiant->getFiliere()->getModules(),
                'canShow' => $soutenanceShow,
                "form"=>$form->createView(),
                'setModel' => false,
                'soutenance' => $soutenance,
                'mesRendus' => [],
                'notesSoutenance' => $notesSoutenance,
                'rendusBySoutenance' => $rendusBySoutenance,
            ]);
        }
    }

    /**
     * @Route("/soutenance/{id}/rendu/add", name="rendu_add_set")
     */
    public function addRenduAction(Request $request, Soutenance $soutenance = null)
    {
        $rendu = new Rendu();
        $form = $this->createForm(RenduFormType::class,$rendu);
        $form->handleRequest($request);
        if(($form->isSubmitted() && $form->isValid())){
            $rendu->setEtudiant($this->getUser()->getEtudiant())
                ->setSoutenance($soutenance)
                ->setRendu($request->files->get("renduFile"),$this->getParameter('rendu_directory'));
            $this->em->persist($rendu);

            $this->em->flush();
            return $this->redirectToRoute('rendu_set',['id' => $soutenance->getId()]);
        }
        return $this->redirectToRoute('rendu_set',['id' => $soutenance->getId()]);
    }

    /**
    * @Route("/soutenance/{id}/rendu/{rendu}/delete", name="rendu_delete")
    */
    public function deleteRenduAction(Soutenance $soutenance = null, Rendu $rendu = null)
    {
        if($rendu->getNote() == null){
            $path = $rendu->getRendu();
            $this->em->remove($rendu);
            $this->em->flush();
            if(file_exists($this->getParameter('rendu_directory').$path)
                &&
                $this->getParameter('rendu_directory')!=$this->getParameter('rendu_directory').$path){
                unlink($this->getParameter('rendu_directory').$path);
            }
        }
        else
            $this->addFlash('error', 'impossible de supprimer un rendu aprés sont évaluuation!');
        return $this->redirectToRoute('rendu_set', ['id' => $soutenance->getId()]);
    }
    /**
     * @Route("/soutenance/{id}/crenau", name="soutenance_creneau_etudiant_show")
     */
    public function showCreneauSoutenanceEtudiant(Soutenance $soutenance){

        $myCreneaus = $this->getUser()->getEtudiant()->getCreneaus();
        $canShow = true;
        foreach ($myCreneaus as $myCreneau){
            if($myCreneau->getSoutenance()->getId() == $soutenance->getId())
                $canShow = false;
        }
        if($canShow) {
            $creneaus = $soutenance->getCreneaus();
            return $this->render('soutenance_etudiant/showCreneauEtudiant.html.twig', [
                'creneaus' => $creneaus,
                'soutenance' => $soutenance,
            ]);
        }
        else
            return $this->redirectToRoute('soutenance_etudiant_show');
    }
    /**
     * @Route("/soutenance/{id}/crenau/{creneau}", name="soutenance_creneau_etudiant_add")
     */
    public function addCreneauSoutenanceEtudiant(Soutenance $soutenance, Creneau $creneau){
        if($creneau->getCapacite() > 0){
            $creneau->setCapacite($creneau->getCapacite() - 1)
                ->addEtudiant($this->getUser()->getEtudiant())
            ;
            $this->em->flush();
            $this->addFlash('success', 'Créneau choisi !');
            return $this->redirectToRoute('soutenance_etudiant_show');
        }

        $this->addFlash('error', 'Le créneau que vous avez choisi est déjà plein vous devez choisir un autre créneau!');
        return $this->redirectToRoute('soutenance_creneau_etudiant_show', ['id' => $soutenance->getId()]);
    }
}
