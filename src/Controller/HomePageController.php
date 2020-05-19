<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     */
    public function index()
    {
        $user = $this->getUser();
        if($user == null)
            return $this->render('index/index.html.twig');
        if($user->getProf() != null)
            return $this->redirectToRoute('soutenance_show');
        if($user->getAdmin() != null)
            return $this->redirectToRoute('diplome_show');
        if($user->getEtudiant() != null)
            return $this->redirectToRoute('soutenance_etudiant_show');
    }
}
