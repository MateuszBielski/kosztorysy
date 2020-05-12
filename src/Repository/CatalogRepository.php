<?php

namespace App\Repository;

use App\Entity\Catalog;
use App\Service\Functions;
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
    public function findByNameDescriptionOld($str)
    {
        return $this->createQueryBuilder('o')
        ->andWhere('o.description like :par1')
        ->setParameter('par1','%'.$str.'%')
        ->orderBy('o.name', 'ASC')
        ->getQuery()
        // ->setMaxResults(10)
        ->getResult();
    }
    public function findByNameDescriptionOld1($stringToExplode)
    {
        $query = $this->createQueryBuilder('o');
        //oddzielnie nazwy katalogów
        if(Functions::IsCatalogName($stringToExplode))
        {
            $strings = explode(' ',$stringToExplode);
            $num = 0;
            foreach($strings as $str)
            {
                $query = $query->setParameter('str'.$num,'%'.$str.'%')->andWhere('o.name LIKE :str'.$num);
                $num++;
            }
        }else
        {

            $strings = explode(' ',$stringToExplode);
            $num = 0;
            foreach($strings as $str)
            {
                $query = $query->setParameter('str'.$num,'%'.$str.'%')->andWhere('o.description LIKE :str'.$num);
                $num++;
            }
        }
        
        return $query->orderBy('o.name', 'ASC')->getQuery()->getResult();
    }
    public function findByNameDescription($stringToExplode)
    {
        $query = $this->createQueryBuilder('o');
        
        $strings = explode(' ',$stringToExplode);
        $num = 0;
        foreach($strings as $str)
        {
            $query = $query->setParameter('str'.$num,'%'.$str.'%')->andWhere('o.description LIKE :str'.$num.' OR o.name LIKE :str'.$num);
            $num++;
        }
        
        return $query->orderBy('o.name', 'ASC')->getQuery()->getResult();
    }
    public function _findByNamePortion(string $stringToExplode)
    {
        $string= explode(" ",$stringToExplode);
        $result = $this->createQueryBuilder('o','o.id');
       
       if (count($string) > 1) {
        $result = $result
            ->where('o.firstName LIKE :string0 or o.surname LIKE :string0')
            ->setParameter('string0', '%'.$string[0].'%')
            ->andWhere('o.firstName LIKE :string1 or o.surname LIKE :string1')
            ->setParameter('string1', '%'.$string[1].'%'); 
       } else {
        $result = $result
            ->where('o.firstName LIKE :string OR o.surname LIKE :string OR o.nrPrawaZawodu LIKE :string')
            ->setParameter('string', '%'.$stringToExplode.'%');
       }
       $result = $result
       ->setMaxResults(50)
       ->getQuery()
       ->getResult();

        return $result;
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
