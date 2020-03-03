<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminCompteFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $admin = new Admin();
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('admin@admin.sou')
            ->setRoles(['ROLE_ADMIN'])
            ->setMailPerso('bahae.zaouch.1@gmail.com')
            ->setPassword($this->encoder->encodePassword($utilisateur, 'admin'))
            ->setNom('admin')
            ->setPrenom('admin')
        ;
        $admin->setCompte($utilisateur);
        $manager->persist($admin);
        $manager->persist($utilisateur);
        $manager->flush();
    }
}
