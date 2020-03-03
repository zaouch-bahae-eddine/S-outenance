<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Form\EtudiantFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EtudiantController
 * @package App\Controller
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class EtudiantController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/etudiant", name="etudiant_show")
     *  @Route("/etudiant/set/{id}", name="etudiant_set")
     */
    public function showEtudiantAction(Etudiant $etudiant = null)
    {
        //dd($etudiant);
        $user = null;
        if($etudiant)
            $user = $etudiant->getCompte();
        $formVide = $this->createForm(EtudiantFormType::class);
        $form = $this->createForm(EtudiantFormType::class,$user);
        if($etudiant)
            $form->get('filiere')->setData($etudiant->getFiliere());
        $repository  = $this->em->getRepository(Etudiant::class);
        $etudiants = $repository->findAll();
        if(!$etudiant)
            return $this->render('etudiant/showEtudiant.html.twig', [
                'etudiants' => $etudiants,
                'etudiantForm' => $formVide->createView(),
                'etudiantFormSet' =>$form->createView(),
                'idEtudiant' => 0,
            ]);
        return $this->render('etudiant/showEtudiant.html.twig', [
            'etudiants' => $etudiants,
            'etudiantForm' => $formVide->createView(),
            'etudiantFormSet' =>$form->createView(),
            'idEtudiant' => $etudiant->getId(),
        ]);

    }
    /**
     * @Route("/etudiant/add", name="etudiant_add")
     * @Route("/etudiant/add/{id}", name="etudiant_add_set")
     */
    public function addEtudiantAction(Request $request, Etudiant $etudiant = null)
    {
        $form = $this->createForm(EtudiantFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $this->em->persist($user);
            if(!$etudiant){
                $etudiant = new Etudiant();
                $etudiant
                    ->setCompte($user)
                    ->setFiliere($form['filiere']->getData());
                $msg = "ajouté";
            }
            else{

                $etudiant
                    ->setCompte($user)
                    ->setFiliere($form['filiere']->getData());
                $msg = "modifié";
            }
            if(!$etudiant->getCompte())
                throw new NotFoundHttpException('compte ne peut pas être null');

            $this->em->persist($etudiant);
            $this->em->flush();
            $this->addFlash('success', 'Etudiant '.$msg);
        }
        return $this->redirectToRoute('etudiant_show');
    }

    /**
     * @Route("/etudiant/remove/{id}", name="etudiant_remove")
     */
    public function removeEtudiantAction(Etudiant $etudiant)
    {
        if(!$etudiant)
            throw new NotFoundHttpException('Auccune etudiant a supprimer');
        $this->em->remove($etudiant);
        $this->em->flush();

        $this->addFlash('success', 'Etudiant supprimé');
        return $this->redirectToRoute('etudiant_show');
    }
    /**
     * @Route("/etudiant/generation-compte", name="etudiant_generate")
     */
    public function GenerateCompteEtudiantAction(){
       /* $etudiants = $this->em->getRepository(Etudiant::class)->findAll();
        $etudiantTab = null;
        $i = 0;
        for($etudiant as $etudiants){
            if($etudiant->compte->password == null)
                $etudiantTab[$i] = $etudiant;
            $i++;
        }
        dd($etudiantTab);*/
    }
}
