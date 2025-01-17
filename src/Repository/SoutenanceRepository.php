<?php

namespace App\Repository;

use App\Entity\Soutenance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Soutenance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Soutenance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Soutenance[]    findAll()
 * @method Soutenance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SoutenanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Soutenance::class);
    }

    // /**
    //  * @return Soutenance[] Returns an array of Soutenance objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Soutenance
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findSoutenancesByModule($moduleId)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.module = :moduleId')
            ->setParameter('moduleId', $moduleId)
            ->getQuery()
            ->getResult()
            ;
    }
}
