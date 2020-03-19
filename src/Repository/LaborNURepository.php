<?php

namespace App\Repository;

use App\Entity\Circulation\Labor_N_U;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Labor_N_U|null find($id, $lockMode = null, $lockVersion = null)
 * @method Labor_N_U|null findOneBy(array $criteria, array $orderBy = null)
 * @method Labor_N_U[]    findAll()
 * @method Labor_N_U[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LaborNURepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Labor_N_U::class);
    }

    // /**
    //  * @return Labor_N_U[] Returns an array of Labor_N_U objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Labor_N_U
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
