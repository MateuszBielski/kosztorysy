<?php

namespace App\Repository;

use App\Entity\ClTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Table|null find($id, $lockMode = null, $lockVersion = null)
 * @method Table|null findOneBy(array $criteria, array $orderBy = null)
 * @method Table[]    findAll()
 * @method Table[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClTable::class);
    }

    // /**
    //  * @return Table[] Returns an array of Table objects
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
    public function findOneBySomeField($value): ?Table
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */ 
    public function findAll()
    {
        return $this->createQueryBuilder('ct')
            // ->where('o.replacedBy is null')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }
    public function findByDescription($stringToExplode)
    {
        $query = $this->createQueryBuilder('ct');
        
        $strings = explode(' ',$stringToExplode);
        $num = 0;
        foreach($strings as $str)
        {
            $query = $query->setParameter('str'.$num,'%'.$str.'%')->andWhere('ct.mainDescription LIKE :str'.$num);
            $num++;
        }
        $query = $query
            ->select('cat,chap,ct')
            ->innerJoin('ct.myChapter','chap')
            ->innerJoin('chap.myCatalog','cat')
            ->orderBy('cat.name','ASC')
            ->addOrderBy('chap.name','ASC')
            ->addOrderBy('ct.myNumber','ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();

        return $query;
    }
}
