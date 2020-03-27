<?php

namespace App\Controller;

use App\Entity\Prof;
use App\Entity\Utilisateur;
use App\Form\GenerationMailFormType;
use App\Form\ProfFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

/**
 * @package App\Controller
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class ProfController extends AbstractController
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
     * @Route("/prof", name="prof_show")
     *  @Route("/prof/set/{id}", name="prof_set")
     */
    public function showProfAction(Prof $prof = null)
    {
        //dd($etudiant);
        $user = null;
        if($prof)
            $user = $prof->getCompte();
        $formVide = $this->createForm(ProfFormType::class);
        $formSuffix = $this->createForm(GenerationMailFormType::class);
        $form = $this->createForm(ProfFormType::class,$user);
        if($prof){
            $form->get('filiere')->setData($prof->getFilieres());
        }
        $repository  = $this->em->getRepository(Prof::class);
        $profs = $repository->findAll();
        if(!$prof)
            return $this->render('prof/showProf.html.twig', [
                'profs' => $profs,
                'profForm' => $formVide->createView(),
                'profFormSet' =>$form->createView(),
                'idProf' => 0,
                'suffixForm' => $formSuffix->createView(),
                'setModel' => false
            ]);
        return $this->render('prof/showProf.html.twig', [
            'profs' => $profs,
            'profForm' => $formVide->createView(),
            'profFormSet' =>$form->createView(),
            'idProf' => $prof->getId(),
            'suffixForm' => $formSuffix->createView(),
            'setModel' => true
        ]);

    }
    /**
     * @Route("/prof/add", name="prof_add")
     * @Route("/prof/add/{id}", name="prof_add_set")
     */
    public function addProfAction(Request $request, Prof $prof = null)
    {
        $form = $this->createForm(ProfFormType::class);
        $form->handleRequest($request);
        $user = null;
        if($form->isSubmitted() && $form->isValid() && ($form->getData()->getEmail() == "" || $prof != null)){
            if(!$prof){
                $user = $form->getData();
                $prof = new Prof();
                $user->setRoles(["ROLE_PROF"]);
                $prof->setCompte($user);
                $filires = $form['filiere']->getData();
                foreach ($filires as $filire) {
                    $prof->addFiliere($filire);
                }
                $msg = "ajouté";
            }
            else{
                $user = $this->em->getRepository(Utilisateur::class)->find($prof->getCompte()->getId());

                $user->setNom($form->getData()->getNom())
                    ->setPrenom($form->getData()->getPrenom())
                    ->setMailPerso($form->getData()->getMailPerso())
                    ->setEmail($form->getData()->getEmail());
                $prof->setcompte($user);
                $filires = $prof->getFilieres();
                foreach ($filires as $filire) {
                    $prof->removeFiliere($filire);
                }
                $filires = $form['filiere']->getData();
                foreach ($filires as $filire)
                    $prof->addFiliere($filire);

                $msg = "modifié";
            }
            if(!$prof->getCompte())
                throw new NotFoundHttpException('compte ne peut pas être null');
            $this->em->persist($user);
            $this->em->persist($prof);
            $this->em->flush();
            $this->addFlash('success', ' Enseignanat '.$msg);
        }
        return $this->redirectToRoute('prof_show');
    }
    /**
     * @Route("/prof/remove/{id}", name="prof_remove")
     */
    public function removeProfAction(Prof $prof)
    {
        if(!$prof)
            throw new NotFoundHttpException('Auccun enseignanat a supprimer');
        $this->em->remove($prof);
        $this->em->remove($prof->getCompte());

        $this->em->flush();

        $this->addFlash('success', 'Ensei   gnant supprimé');
        return $this->redirectToRoute('prof_show');
    }
    /**
     * @Route("/prof/generation-compte", name="prof_generate")
     */
    public function GenerateCompteProfAction(Request $request, UserPasswordEncoderInterface $encoder){
        $form = $this->createForm(GenerationMailFormType::class);
        $faker = Faker\Factory::create();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $profs = $this->em->getRepository(Prof::class)->findAll();
            $i = 0;
            foreach($profs as $prof){
                if($prof->getCompte()->getPassword() == null) {
                    $prof->getCompte()->setEmail($prof->getCompte()->getPrenom() .'.'.$prof->getCompte()->getNom().'@'. $form->getData()['Suffix'] . '.fr')
                        ->setPassword($encoder->encodePassword($prof->getCompte(), $faker->password));
                    dump($prof->getCompte());

                    $this->em->persist($prof->getCompte());
                    $i++;
                }
            }
            $this->em->flush();
            if($i>0)
                $this->addFlash('success', $i.' Comptes a été génerer');
        }
        return $this->redirectToRoute('prof_show');
    }
}
