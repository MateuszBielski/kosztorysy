<?php

namespace App\Repository;

use App\Entity\Catalog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Catalog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Catalog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Catalog[]    findAll()
 * @method Catalog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatalogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Catalog::class);
    }
    public function findAllIndexedByName()
    {
        return $this->createQueryBuilder('o','o.name')
            // ->where('o.replacedBy is null')
            ->getQuery()
            ->getResult();
    }
    public function findAllByName()
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findByNameDescription($str)
    {
        return $this->createQueryBuilder('o')
        ->andWhere('o.description like :par1')
        ->setParameter('par1','%'.$str.'%')
        ->orderBy('o.name', 'ASC')
        ->getQuery()
        // ->setMaxResults(10)
        ->getResult();
    }
    // /**
    //  * @return Catalog[] Returns an array of Catalog objects
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
    public function findOneBySomeField($value): ?Catalog
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
