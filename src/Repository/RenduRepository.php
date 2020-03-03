<?php

namespace App\Repository;

use App\Entity\Rendu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Rendu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rendu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rendu[]    findAll()
 * @method Rendu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RenduRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rendu::class);
    }

    // /**
    //  * @return Rendu[] Returns an array of Rendu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Rendu
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
