<?php

namespace App\Repository;

use App\Entity\Circulation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Circulation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Circulation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Circulation[]    findAll()
 * @method Circulation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CirculationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Circulation::class);
    }

    // /**
    //  * @return Circulation[] Returns an array of Circulation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Circulation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
