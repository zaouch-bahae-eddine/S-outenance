<?php

namespace App\Controller;

use App\Entity\Rendu;
use App\Entity\Soutenance;
use App\Form\RenduFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/etudiant")
 */
class RenduController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function showRenduAction(Soutenance $soutenance, Rendu $rendu = null)
    {

        $form = $this->createForm(RenduFormType::class,$rendu);
        return $this->render("rendu/showRendu.html.twig",[
            "form"=>$form->createView(),
            "idSoutenance" => $soutenance->getId(),
            ]);
    }
    public function addRenduAction(Request $request, Soutenance $soutenance = null, Rendu $rendu = null)
    {
        if(!$rendu)
            $rendu = new Rendu();
        $form = $this->createForm(RenduFormType::class,$rendu);
        $form->handleRequest($request);
        if(($form->isSubmitted() && $form->isValid())){
            $rendu->setEtudiant($this->getUser()->getEtudiant())
                ->setSoutenance($soutenance)
                ->setRendu($request->files->get("renduFile"),$this->getParameter('rendu_directory'));
            dd($request->files->get("renduFile"));
            $this->em->persist($rendu);

            $this->em->flush();
            return $this->redirectToRoute('rendu_add_set',['id' => $soutenance->getId(),"rendu"=>$rendu->getId()]);
        }

        return $this->redirectToRoute('rendu_add',['id' => $soutenance->getId()]);
    }
}
