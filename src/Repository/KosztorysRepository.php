<?php

namespace App\Repository;

use App\Entity\Kosztorys;
use App\Entity\PozycjaKosztorysowa;
use App\Entity\TableRow;
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
    public function findLoadingFieldsSeparately(int $id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            "SELECT 
            pk.id as pk_id,
            tr.myNumber, 
            tr.subDescription, 
            ct.mainDescription, 
            ct.myNumber as ct_myNumber ,
            cp.name as cp_name,
            cat.name as cat_name,
            pk.obmiar
            FROM 
            App\Entity\PozycjaKosztorysowa pk
            INNER JOIN App\Entity\TableRow tr WITH pk.podstawaNormowa = tr
            INNER JOIN App\Entity\ClTable ct WITH tr.myTable = ct 
            INNER JOIN App\Entity\Chapter cp WITH ct.myChapter = cp
            INNER JOIN App\Entity\Catalog cat WITH cp.myCatalog = cat
            WHERE pk.kosztorys = $id"
            );
        $results = $query->getResult();

        $kosztorys = new Kosztorys;
        $kosztorys->setId($id);
        foreach($results as $result)
        {
            $pozycja = new PozycjaKosztorysowa;
            $pozycja->CreateDependecyForRenderAndTest($result);
            $kosztorys->addPozycjeKosztorysowe($pozycja);
        }
        return $kosztorys;
    }
}
