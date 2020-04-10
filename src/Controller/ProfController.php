<?php

namespace App\Controller;

use App\Entity\Prof;
use App\Entity\Soutenance;
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
                $form->getData()->setEmailToNull();
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
                if($form->getData()->getEmail() != "")
                    $user->setEmail($form->getData()->getEmail());
                else
                    $user->setEmailToNull();
                $user->setNom($form->getData()->getNom())
                    ->setPrenom($form->getData()->getPrenom())
                    ->setMailPerso($form->getData()->getMailPerso());
                $prof->setcompte($user);
                $filires = $prof->getFilieres();
                foreach ($filires as $filire)
                    $prof->removeFiliere($filire);

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
        $deathProf = $prof;
        $rep = $this->em->getRepository(Soutenance::class);

        $soutenances = $rep->findBy(['prof'=> $prof]);
        foreach ($soutenances as $soutenance){
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
            $this->em->flush();
        }
        $this->em->remove($prof);
        $this->em->remove($deathProf->getCompte());
        $this->em->flush();

        $this->addFlash('success', 'Enseignant supprimé !');
        return $this->redirectToRoute('prof_show');
    }
    /**
     * @Route("/prof/generation-compte", name="prof_generate")
     */
    public function GenerateCompteProfAction(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer){
        $form = $this->createForm(GenerationMailFormType::class);
        $faker = Faker\Factory::create();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $profs = $this->em->getRepository(Prof::class)->findAll();
            $i = 0;
            foreach($profs as $prof){
                if($prof->getCompte()->getPassword() == null) {
                    $password = $faker->password;
                    $prof->getCompte()->setEmail($prof->getCompte()->getPrenom() .'.'.$prof->getCompte()->getNom().'@'. $form->getData()['Suffix'] . '.fr')
                        ->setPassword($encoder->encodePassword($prof->getCompte(), $password));
                    $message = (new \Swift_Message('Compte Soutenance Plate-forme'))
                        ->setFrom(['ne-pas-repondre@gmail.com' => 'ne-pas-repondre@'.$form->getData()['Suffix'] . '.fr'])
                        ->setTo($prof->getCompte()->getMailPerso())
                        ->setBody(
                            $this->renderView(
                            // templates/emails/registration.html.twig
                                'email/registration.html.twig',
                                [
                                    'nom' => $prof->getCompte()->getNom(),
                                    'prenom' => $prof->getCompte()->getPrenom(),
                                    'mail' => $prof->getCompte()->getEmail(),
                                    'password' => $password,
                                ]
                            ),
                            'text/html'
                        );

                    $this->em->persist($prof->getCompte());
                    $mailer->send($message);
                    $i++;
                }
            }
            $this->em->flush();
            if($i>0)
                $this->addFlash('success', $i.' Comptes a été génerer Comptes a été génerer et envoyer aux adresse mail personelle de chaqu\'un');
        }
        return $this->redirectToRoute('prof_show');
    }
}
