<?php

namespace App\Controller;

use App\Entity\Creneau;
use App\Entity\Note;
use App\Entity\Soutenance;
use App\Form\CreneauFormType;
use App\Form\SoutenanceBaseFormType;
use App\Form\SoutenanceEvaluateurFormType;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @Route("/soutenance/set/{id}-{nb}", name="soutenance_set")
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
    /**
     * @Route("/soutenance/{id}/creneau", name="soutenance_creneau_show")
     *  @Route("/soutenance/{id}/creneau/set/{creneau}", name="soutenance_creneau_set")
     */
    public function showCreneaauToSoutenanceAction(Soutenance $soutenance, Creneau $creneau = null)
    {
        $formVide = $this->createForm(CreneauFormType::class);
        $form = $this->createForm(CreneauFormType::class,$creneau);
        $creneaux = $soutenance->getCreneaus();
        if(!$creneau)
            return $this->render('soutenance/showCreneau.html.twig', [
                'creneaux' => $creneaux,
                'creneauForm' => $formVide->createView(),
                'creneauFormSet' =>$form->createView(),
                'idSoutenance' => $soutenance->getId(),
                'idCreneau' => 0,
                'setModel' => false,
            ]);
        return $this->render('soutenance/showCreneau.html.twig', [
            'creneaux' => $creneaux,
            'creneauForm' => $formVide->createView(),
            'creneauFormSet' =>$form->createView(),
            'idSoutenance' => $soutenance->getId(),
            'idCreneau' => $creneau->getId(),
            'setModel' => true,
        ]);
    }
    /**
     * @Route("/soutenance/{id}/creneau/add", name="soutenance_creneau_add")
     * @Route("/soutenance/{id}/creneau/add/{creneau}", name="soutenance_creneau_add_set")
     */
    public function addCreneaauToSoutenanceAction(Request $request, Soutenance $soutenance, Creneau $creneau = null)
    {
        if($creneau != null)
            foreach ($soutenance->getCreneaus() as $cr){
                if($cr->getId() == $creneau->getId()){
                    foreach ($cr->getSalles() as $salle){
                        $creneau->removeSalle($salle);
                    }
                }
            }

        $form = $this->createForm(CreneauFormType::class,$creneau);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            if(!$creneau){
                /**
                 * @var Creneau $creneau
                 */
                $creneau = $form->getData();
                $creneau->setSoutenance($soutenance);
                $msg = "ajouté";
            }
            else{

                foreach ($creneau->getSalles() as $salle){
                    $salle->addCreneau($creneau);
                }
                $msg = "modifié";
            }
            if(!$creneau->getDate())
                throw new NotFoundHttpException('Date ne peut pas être null');

            $this->em->persist($creneau);
            $this->em->flush();
            $this->addFlash('success', 'Créneau '.$msg);
        }
        return $this->redirectToRoute('soutenance_creneau_show',['id' => $soutenance->getId()]);
    }

    /**
     * @Route("/soutenance/{id}/creneau/remove/{creneau}", name="soutenance_creneau_remove")
     */
    public function removeCreneau(Soutenance $soutenance, Creneau $creneau){
        $soutenance->removeCreneau($creneau);
        $this->em->flush();
        return $this->redirectToRoute('soutenance_creneau_show',['id' => $soutenance->getId()]);
    }

    /**
     * @Route("/soutenance/{id}/note", name="soutenance_note_show")
     */
    public function showNoteAction(Soutenance $soutenance)
    {

        if($soutenance->getProf()->getId() == $this->getUser()->getProf()->getId())
            $all = true;
        else
            $all = false;
        $etudiants = $soutenance->getModule()->getFiliere()->getEtudiants();
        foreach ($etudiants as $etudiant){
            foreach ($soutenance->getEvaluateurs() as $evaluateur){
                if($all)
                    $tab[$etudiant->getId()][$evaluateur->getId()] = 0;
                $tab[$etudiant->getId()]["me"] = 0;
                foreach ($etudiant->getNotes() as $note){
                    if($all)
                        if($note->getProf()->getId() == $evaluateur->getId())
                            $tab[$etudiant->getId()][$evaluateur->getId()] = $note;
                    if($note->getProf()->getId() == $this->getUser()->getProf());
                        $tab[$etudiant->getId()]["me"] = $note;
                }
            }
        }
        $notes = $soutenance->getNotes();
            return $this->render('soutenance/showNote.html.twig', [
                'notes' => $etudiant->getNotes(),
                'etudiants' => $etudiants,
                'evaluateurs' => $soutenance->getEvaluateurs(),
                'soutenance' => $soutenance,
                'tab' => $tab,
                'all' => $all
            ]);
    }
    /**
     * @Route("/soutenance/{id}/note/add", name="soutenance_note_add")
     */
    public function addNoteAction(Request $request, Soutenance $soutenance)
    {
        return $this->redirectToRoute('soutenance_creneau_show',['id' => $soutenance->getId()]);
    }


}
