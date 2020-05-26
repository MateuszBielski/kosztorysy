<?php

namespace App\Repository;

use App\Entity\Circulation\CirculationNameAndUnit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CirculationNameAndUnit|null find($id, $lockMode = null, $lockVersion = null)
 * @method CirculationNameAndUnit|null findOneBy(array $criteria, array $orderBy = null)
 * @method CirculationNameAndUnit[]    findAll()
 * @method CirculationNameAndUnit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CirculationNameAndUnitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CirculationNameAndUnit::class);
    }

    // /**
    //  * @return CirculationNameAndUnit[] Returns an array of CirculationNameAndUnit objects
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
    public function findOneBySomeField($value): ?CirculationNameAndUnit
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
