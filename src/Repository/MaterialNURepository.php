<?php

namespace App\Repository;

use App\Entity\Circulation\Material_N_U;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Material_N_U|null find($id, $lockMode = null, $lockVersion = null)
 * @method Material_N_U|null findOneBy(array $criteria, array $orderBy = null)
 * @method Material_N_U[]    findAll()
 * @method Material_N_U[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialNURepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Material_N_U::class);
    }

    // /**
    //  * @return Material_N_U[] Returns an array of Material_N_U objects
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
    public function findOneBySomeField($value): ?Material_N_U
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
