<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SoutenanceEtudiantController extends AbstractController
{
    /**
     * @Route("/soutenance/etudiant", name="soutenance_etudiant")
     */
    public function index()
    {
        return $this->render('soutenance_etudiant/index.html.twig', [
            'controller_name' => 'SoutenanceEtudiantController',
        ]);
    }
}
