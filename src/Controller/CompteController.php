<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class CompteController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class CompteController extends AbstractController
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
     * @Route("/compte", name="show_compte")
     */
    public function index()
    {
        $user = $this->getUser()->getUsername();
        return $this->render('compte/showCompte.html.twig', [
            'userName' => $user,
        ]);
    }
    /**
     * @Route("/compte/set", name="set_compte")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        if ($encoder->isPasswordValid($this->getUser(),$request->request->get("sy-id-old"))
            && $request->request->get("sy-id-new") != ''
            && $request->request->get("sy-id-new") === $request->request->get("sy-id-new-2")
        ){
            $this->getUser()->setPassword($encoder->encodePassword($this->getUser(),$request->request->get("sy-id-new")));
            $this->em->persist($this->getUser());
            $this->em->flush();

            $this->addFlash('success', 'Mot de passe modifié');
        }
        else if (!$encoder->isPasswordValid($this->getUser(),$request->request->get("sy-id-old")) )
            $this->addFlash('error', 'Votre ancien mot de passe n\' est pas correcte !');
        else
            $this->addFlash('error', 'Le nouveau mot de passe n\'est pas conforme! entrez un autre s\'il vous plez');
        return $this->redirectToRoute("show_compte");
    }
}
