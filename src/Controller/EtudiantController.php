<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\Utilisateur;
use App\Form\EtudiantFormType;
use App\Form\GenerationMailFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
        $formSuffix = $this->createForm(GenerationMailFormType::class);
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
                'suffixForm' => $formSuffix->createView(),
                'setModel' => false,
            ]);
        return $this->render('etudiant/showEtudiant.html.twig', [
            'etudiants' => $etudiants,
            'etudiantForm' => $formVide->createView(),
            'etudiantFormSet' =>$form->createView(),
            'idEtudiant' => $etudiant->getId(),
            'suffixForm' => $formSuffix->createView(),
            'setModel' => true,
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
        $user = null;
        if($form->isSubmitted() && $form->isValid() && ($form->getData()->getEmail() == "" || $etudiant != null)){
            if(!$etudiant){
                $user = $form->getData();
                $etudiant = new Etudiant();
                $user->setRoles(["ROLE_ETUDIANT"]);
                $etudiant
                    ->setCompte($user)
                    ->setFiliere($form['filiere']->getData());
                $msg = "ajouté";
            }
            else{
                $user = $this->em->getRepository(Utilisateur::class)->find($etudiant->getCompte()->getId());

                $user->setNom($form->getData()->getNom())
                ->setPrenom($form->getData()->getPrenom())
                ->setMailPerso($form->getData()->getMailPerso())
                ->setEmail($form->getData()->getEmail());
                $etudiant
                    ->setFiliere($form['filiere']->getData())
                    ->setcompte($user);
                ;
                $msg = "modifié";
            }
            if(!$etudiant->getCompte())
                throw new NotFoundHttpException('compte ne peut pas être null');
            $this->em->persist($user);
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
            throw new NotFoundHttpException('Auccun etudiant a supprimer');
        $this->em->remove($etudiant);
        $this->em->remove($etudiant->getCompte());

        $this->em->flush();

        $this->addFlash('success', 'Etudiant supprimé');
        return $this->redirectToRoute('etudiant_show');
    }
    /**
     * @Route("/etudiant/generation-compte", name="etudiant_generate")
     */
    public function GenerateCompteEtudiantAction(Request $request, UserPasswordEncoderInterface $encoder){
        $form = $this->createForm(GenerationMailFormType::class);
        $faker = Faker\Factory::create();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
           $etudiants = $this->em->getRepository(Etudiant::class)->findAll();
            $etudiantTab = [];
            $i = 0;
            foreach($etudiants as $etudiant){
                if($etudiant->getCompte()->getPassword() == null) {
                    $etudiant->getCompte()->setEmail('e' . ($i + 1) . '@' . $form->getData()['Suffix'] . '.fr')
                        ->setPassword($encoder->encodePassword($etudiant->getCompte(), $faker->password));
                    dump($etudiant->getCompte());

                    $this->em->persist($etudiant->getCompte());
                    $i++;
                }
            }
            $this->em->flush();
            if($i>0)
            $this->addFlash('success', $i.' Comptes a été génerer');
        }
        return $this->redirectToRoute('etudiant_show');
    }
}
