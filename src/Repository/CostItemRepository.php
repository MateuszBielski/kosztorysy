<?php

namespace App\Repository;

use App\Entity\CostItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CostItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CostItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CostItem[]    findAll()
 * @method CostItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CostItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CostItem::class);
    }

    // /**
    //  * @return CostItem[] Returns an array of CostItem objects
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
    public function findOneBySomeField($value): ?CostItem
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
