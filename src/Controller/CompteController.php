<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CompteController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class CompteController extends AbstractController
{
    /**
     * @Route("/compte", name="show_compte")
     */
    public function index()
    {
        $user = $this->getUser()->getUsername();
        return $this->render('compte/showCompte.html.twig', [
            'userName' => $user,
        ]);
    }
}
