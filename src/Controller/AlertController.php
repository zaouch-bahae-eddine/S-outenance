<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AlertController extends AbstractController
{
    /**
     * @Route("/alert", name="alert")
     */
    public function index()
    {
        return $this->render('alert/index.html.twig', [
            'controller_name' => 'AlertController',
        ]);
    }
}
