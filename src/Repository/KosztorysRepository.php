<?php

namespace App\Repository;

use App\Entity\Kosztorys;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Kosztorys|null find($id, $lockMode = null, $lockVersion = null)
 * @method Kosztorys|null findOneBy(array $criteria, array $orderBy = null)
 * @method Kosztorys[]    findAll()
 * @method Kosztorys[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KosztorysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Kosztorys::class);
    }

    // /**
    //  * @return Kosztorys[] Returns an array of Kosztorys objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Kosztorys
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
