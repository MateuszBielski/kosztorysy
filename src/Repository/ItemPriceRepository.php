<?php

namespace App\Repository;

use App\Entity\ItemPrice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ItemPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemPrice[]    findAll()
 * @method ItemPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemPriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemPrice::class);
    }

    // /**
    //  * @return ItemPrice[] Returns an array of ItemPrice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ItemPrice
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findByPriceListAndCircNU($priceListName, array $cirNUids)
    {
        return $this->createQueryBuilder('i')//,'nau.id'
        ->select( 'i.priceValue' )
        // ->leftJoin()
        ->setParameter('plName',$priceListName)
        ->innerJoin('i.priceList','priceList')
        ->where('priceList.name = :plName')
        ->setParameter('cirIds',array_values($cirNUids))
        ->innerJoin('i.nameAndUnit','nau')
        ->andWhere("nau.id IN(:cirIds)")
        ->getQuery()
        ->getResult();
        // ->where("user.id IN(:usersIds)")
        // ->setParameter('usersIds',array_values($usersId))
        // SELECT i.priceValue FROM App\Entity\ItemPrice i INDEX BY nau.id INNER JOIN i.priceList priceList INNER JOIN i.nameAndUnit nau WHERE priceList.name = :plName AND nau.id IN(:cirIds)
    }
}
