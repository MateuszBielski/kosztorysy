<?php

namespace App\Repository;

use App\Entity\TableRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TableRow|null find($id, $lockMode = null, $lockVersion = null)
 * @method TableRow|null findOneBy(array $criteria, array $orderBy = null)
 * @method TableRow[]    findAll()
 * @method TableRow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TableRow::class);
    }
    /**
     * @return TableRow[]
     */
    public function findByDescriptionFragment($fragment)
    {
        return $this->createQueryBuilder('tr')
            ->leftJoin('tr.myTable','myTable')
            ->where('tr.subDescription LIKE :val or myTable.mainDescription LIKE :val')
            ->setParameter('val', '%'.$fragment.'%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return TableRow[] Returns an array of TableRow objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TableRow
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
