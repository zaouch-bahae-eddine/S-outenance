<?php

namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    // /**
    //  * @return Note[] Returns an array of Note objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function findNoteByProfAndSoutenance($etudiantId, $profId, $soutenanceId)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.etudiant = :etudiantId')
            ->andWhere('n.prof = :profId')
            ->andWhere('n.soutenance = :soutenanceId')
            ->setParameter('profId', $profId)
            ->setParameter('soutenanceId', $soutenanceId)
            ->setParameter('etudiantId', $etudiantId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }



}
