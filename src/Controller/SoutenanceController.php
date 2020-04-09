<?php

namespace App\Controller;

use App\Entity\Creneau;
use App\Entity\Note;
use App\Entity\Rendu;
use App\Entity\Soutenance;
use App\Form\CreneauFormType;
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
     * @Route("/soutenance/set/{id}-{nb}", name="soutenance_set")
     */
    public function showSoutenanceAction(Soutenance $soutenance = null, $nb = 0)
    {
        //ghalat!! rah yjib ga3 les soutenances
        $formVide = $this->createForm(SoutenanceBaseFormType::class);
        $form = $this->createForm(SoutenanceBaseFormType::class,$soutenance);
        $formEvaluateur = $this->createForm(SoutenanceEvaluateurFormType::class,$soutenance);
        $repository  = $this->em->getRepository(Soutenance::class);
        $soutenances = $repository->findAll();
        $i = 0;
        foreach ($soutenances as $sou){
            if($sou->getProf()->getId() == $this->getUser()->getProf()->getId()){
                $soutenanceTab[$i] = $sou;
                $mySoutenance[$sou->getId()] = 1;
                $i++;
            }
            else
                foreach ($soutenance->getEvaluateurs() as $evaluateur){
                    if($evaluateur->getId() == $this->getUser()->getProf()->getId()){
                        $soutenanceTab[$i] = $sou;
                        $mySoutenance[$sou->getId()] = 0;
                        $i++;
                    }
                }
        }
        if(!$soutenance)
            return $this->render('soutenance/showSoutenance.html.twig', [
                'soutenances' => $soutenanceTab,
                'mySoutenance' => $mySoutenance,
                'soutenanceForm' => $formVide->createView(),
                'soutenanceFormSet' =>$form->createView(),
                'soutenanceEvaluateurFormSet' => $formEvaluateur->createView(),
                'idSoutenance' => 0,
                'setModel' => false,
            ]);
        return $this->render('soutenance/showSoutenance.html.twig', [
            'soutenances' => $soutenanceTab,
            'mySoutenance' => $mySoutenance,
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
/*
        $form = null;
        if($nb == 1)*/
            $form = $this->createForm(SoutenanceBaseFormType::class);

        if($nb == 2)
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
                if($nb == 1)
                    $soutenance->setNom($form->getData()->getNom())
                        ->setAlerte($form->getData()->getAlerte())
                        ->setModule($form->getData()->getModule());
                if($nb == 2){
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
        $evaluateurs = $soutenance->getEvaluateurs();

        $rep = $this->em->getRepository(Note::class);
        foreach ($etudiants as $etudiant){
            foreach ($evaluateurs as $evaluateur){
                if($all){
                    $note = $rep->findNoteByProfAndSoutenance($etudiant->getId(),$evaluateur->getId(), $soutenance->getId());
                    $tab[$etudiant->getId()][$evaluateur->getId()] = $note == null? 0 : $note->getNote();
                }
                $note = $rep->findNoteByProfAndSoutenance($etudiant->getId(),$this->getUser()->getProf()->getId(), $soutenance->getId());
                $tab[$etudiant->getId()]["me"] = $note == null? 0 : $note->getNote();
            }
        }
            return $this->render('soutenance/showNote.html.twig', [
                'etudiants' => $etudiants,
                'evaluateurs' => $evaluateurs,
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
        $etudiants = $soutenance->getModule()->getFiliere()->getEtudiants();
        foreach ($etudiants as $etudiant){
            $note = $this->em->getRepository(Note::class)
                ->findNoteByProfAndSoutenance($etudiant->getId(), $this->getUser()->getProf()->getId(),$soutenance->getId());
            if(!$note) {
                $note = new Note();
                $note->setSoutenance($soutenance)
                    ->setProf($this->getUser()->getProf())
                    ->setEtudiant($etudiant);
                $this->em->persist($note);
            }
            $note->setNote(floatval($request->request->get('id-'.$etudiant->getId())));
        }
        $this->em->flush();

        $this->addFlash('success', 'Note Enregistrés');
        return $this->redirectToRoute('soutenance_note_show',['id' => $soutenance->getId()]);
    }
    /**
     * @Route("/soutenance/{id}/rendu", name="soutenance_rendu_note_show")
     */
    public function showRenduAction(Soutenance $soutenance)
    {
        if($soutenance->getProf()->getId() == $this->getUser()->getProf()->getId())
            $all = true;
        else
            $all = false;
        $etudiants = $soutenance->getModule()->getFiliere()->getEtudiants();
        $rep = $this->em->getRepository(Rendu::class);
        foreach ($etudiants as $etudiant){
            $rendu = $rep->findBy(['soutenance'=>$soutenance,'etudiant' => $etudiant]);
                    $rendus[$etudiant->getId()] = $rendu;
        }
        return $this->render('soutenance/showRendu.html.twig', [
            'etudiants' => $etudiants,
            'soutenance' => $soutenance,
            'rendus' => $rendus,
            'all' => $all
        ]);
    }
    /**
     * @Route("/soutenance/{id}/rendu/note/add", name="soutenance_rendu_note_add")
     */
    public function addRenduNoteAction(Request $request, Soutenance $soutenance)
    {

        if($soutenance->getProf()->getId() != $this->getUser()->getProf()->getId())
            throw new NotFoundHttpException('vous etes pas la bonne personne pour cette action');

        $etudiants = $soutenance->getModule()->getFiliere()->getEtudiants();
        foreach ($etudiants as $etudiant){
            $rendus = $this->em->getRepository(Rendu::class)->findBy(['soutenance'=>$soutenance,'etudiant' => $etudiant]);
            foreach ($rendus as $rendu){
                $rendu->setNote(floatval($request->request->get('id-'.$rendu->getId())));
            }
        }
        $this->em->flush();
        $this->addFlash('success', 'Note Enregistrés ');
        return $this->redirectToRoute('soutenance_rendu_note_show',['id' => $soutenance->getId()]);
    }
    /**
     * @Route("/soutenance/impression", name="soutenance_impression")
     */
    public function impression(Request $request){
        $soutenances = $this->em->getRepository(Soutenance::class)->findAll();
        $i = 0;
        foreach ($soutenances as $soutenance){
            if($soutenance->getProf()->getId() == $this->getUser()->getProf()->getId()){
                $soutenanceTab[$i] = $soutenance;
                $i++;
            }
            else
            foreach ($soutenance->getEvaluateurs() as $evaluateur){
                if($evaluateur->getId() == $this->getUser()->getProf()->getId()){
                    $soutenanceTab[$i] = $soutenance;
                    $i++;
                }
            }
        }
        return $this->render('imprimer/planning.html.twig',[
            'soutenances' => $soutenanceTab,
        ]);
    }
    /**
     * @Route("/soutenance/{id}/alerte", name="alerte_etudiant")
     */
    public function alerteEtudiant(Request $request, Soutenance $soutenance, \Swift_Mailer $mailer)
    {
        $etudiants = $soutenance->getModule()->getFiliere()->getEtudiants();
        foreach ($etudiants as $etudiant){
            $message = (new \Swift_Message('Alerte Soutenance Plate-forme'))
                ->setFrom([$etudiant->getCompte()->getEmail() => 'Alerte@ne-pas-repondre.fr'])
                ->setTo($etudiant->getCompte()->getMailPerso())
                ->setBody(
                    $this->renderView(
                    // templates/emails/registration.html.twig
                        'email/alerte.html.twig',
                        [
                            'nom' => $etudiant->getCompte()->getNom(),
                            'prenom' => $etudiant->getCompte()->getPrenom(),
                            'messageProf' => $request->request->get('msg-alerte'),
                            'nomProf' => $this->getUser()->getNom(),
                            'prenomProf'=> $this->getUser()->getPrenom(),
                            'soutenance' => $soutenance->getNom(),
                        ]
                    ),
                    'text/html'
                );
            $mailer->send($message);
        }
        $this->addFlash('success', 'Message Envoyé !');
        return $this->redirectToRoute('soutenance_show');
    }

    /**
     * @Route("/soutenance/{id}/romove",name="remove_soutenance")
     */
    public function removeSoutenance(Soutenance $soutenance = null){
        if($soutenance != null){
            $rendus = $soutenance->getRendus();
            foreach ($rendus as $rendu){
                $path = $rendu->getRendu();
                $this->em->remove($rendu);
                $this->em->flush();
                if(file_exists($this->getParameter('rendu_directory').$path)
                    &&
                    $this->getParameter('rendu_directory')!=$this->getParameter('rendu_directory').$path){
                    unlink($this->getParameter('rendu_directory').$path);
                }

            }
            $this->em->remove($soutenance);
        }
        $this->em->flush();
        $this->addFlash('success', 'Soutenance Supprimé !');
        return $this->redirectToRoute("soutenance_show");
    }
}
