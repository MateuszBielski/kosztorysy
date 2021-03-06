<?php

namespace App\Repository;

use App\Entity\ItemPriceOld;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @method ItemPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemPrice[]    findAll()
 * @method ItemPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * 
 */
class ItemPriceOldRepository extends ServiceEntityRepository
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
    public function findAll()
    {
        # code...
    }
    public function findByPriceListOld($priceListName, array $cirNUids)
    {
        $ids = '';
        foreach ($cirNUids as $id) $ids .= "$id,";
        $ids = trim($ids, ',');

        $OR_ids = '';
        foreach ($cirNUids as $id) $OR_ids .= "cnu.id = $id OR ";
        $OR_ids = trim($OR_ids, 'OR ');
        $em = $this->getEntityManager();
        /*
        $query = $em->createQuery(
            "SELECT i.priceValue, nau.id FROM App\Entity\ItemPrice i
            INNER JOIN i.priceList pl 
            INNER JOIN i.nameAndUnit nau
            WHERE pl.name = '$priceListName'
            AND nau.id IN ($ids)"
        );
        
        $query = $em->createQuery(
            "SELECT i.priceValue, nau.id FROM App\Entity\ItemPrice i
            LEFT JOIN i.nameAndUnit nau
            LEFT JOIN i.priceList pl 
            WHERE pl.name = '$priceListName'
            AND $OR_ids"
        );
        */
        $query = $em->createQuery(
            "SELECT i.priceValue, cnu.id FROM App\Entity\Circulation\CirculationNameAndUnit cnu
            INNER JOIN App\Entity\ItemPrice i WITH i.nameAndUnit = cnu
            LEFT JOIN i.priceList pl 
            WHERE pl.name = '$priceListName'
            AND $OR_ids"
        );
        
        $rawResults = $query->getResult();
        $resArray = array();
        foreach($rawResults as $rr)
        {
            $resArray[$rr['id']] = $rr['priceValue'];
        }
        return $resArray;
    }
   
    public function findByPriceListAndNameUnitIds($priceListName, array $cirNUids)
    {
        $ids = '';
        foreach ($cirNUids as $id) $ids .= "$id,";
        $ids = trim($ids, ',');
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('price_value', 'pv');
        $rsm->addScalarResult('name_and_unit_id', 'nau_id');
        $em = $this->getEntityManager();
        $query = $em->createNativeQuery(
            "SELECT ip.price_value,ci.name_and_unit_id 
            FROM item_price ip INNER JOIN circulation ci ON ci.id = ip.id  
            INNER JOIN price_list pl on pl.id = ip.price_list_id
            WHERE ci.name_and_unit_id IN ($ids)
            AND pl.name = '$priceListName'
            ",$rsm);
        $rawResults = $query->getResult();
        $resArray = array();
        foreach($rawResults as $rr)
        {
            $resArray[$rr['nau_id']] = $rr['pv'];
        }
        return $resArray;
    }
}
