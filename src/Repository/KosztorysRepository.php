<?php

namespace App\Repository;

use App\Entity\Circulation\Material;
use App\Entity\Kosztorys;
use App\Entity\PozycjaKosztorysowa;
use App\Entity\TableRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
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
        
        $results = $this->getIdNumberDescriptionsNames_PozycjiKosztorysowychDlaKosztorysu($id);
        // print_r($results);
        $kosztorys = new Kosztorys;
        $kosztorys->setId($id);
        // $kosztorys = 
        // $listaCenId = $results['listaCenId'];
        // $kosztorys->setPoczatkowaListaCen($listaCenId);
        foreach($results as $result)
        {
            $pozycja = new PozycjaKosztorysowa;
            $pozycja->CreateDependecyForRenderAndTest($result);
            $kosztorys->addPozycjeKosztorysowe($pozycja);
        }
        
        
        return $kosztorys;
    }
    public function getIdNumberDescriptionsNames_PozycjiKosztorysowychDlaKosztorysu(int $id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            "SELECT 
            pk.id as pk_id,
            tr.myNumber,
            tr.unit,
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
        $result = $query->getResult();
        return $result;
    }
    public function getListaCenIdDlaKosztorysu($id)
    {
        $em = $this->getEntityManager();
        $query = "SELECT k.poczatkowa_lista_cen_id AS listaCenId FROM kosztorys k WHERE k.id =  $id";
        $rsm = new ResultSetMapping;
        $rsm->addScalarResult('listaCenId', 'listaCenId');
        return $em->createNativeQuery($query,$rsm)->getResult()[0]['listaCenId'];
    }
    public function getPkIdWartoscCenaDlaKosztorysIlistaCen($k_id, $l_id)
    {
        $em = $this->getEntityManager();
        $query = "SELECT sub.pk_id,sub.r,cnu.unit as u,ip.price_value,c.value FROM 
        (
            SELECT mat.id AS cir_id, pk.kosztorys_id AS koszt_id, pk.id AS pk_id,'m' as r FROM material mat  
            JOIN pozycja_kosztorysowa pk ON pk.podstawa_normowa_id = mat.table_row_id
            UNION 
            SELECT equ.id AS cir_id, pk.kosztorys_id AS koszt_id, pk.id AS pk_id,'e' as r FROM equipment equ
            JOIN pozycja_kosztorysowa pk ON pk.podstawa_normowa_id = equ.table_row_id
            UNION 
            SELECT lab.id AS cir_id, pk.kosztorys_id AS koszt_id, pk.id AS pk_id,'l' as r FROM labor lab
            JOIN pozycja_kosztorysowa pk ON pk.podstawa_normowa_id = lab.table_row_id
        ) AS sub
        JOIN circulation c ON sub.cir_id = c.id
        JOIN circulation_name_and_unit cnu ON c.name_and_unit_id = cnu.id
        JOIN item_price ip ON cnu.id = ip.name_and_unit_id
        WHERE sub.koszt_id = $k_id AND ip.price_list_id = $l_id";
        $rsm = new ResultSetMapping;
        $rsm->addScalarResult('pk_id', 'pk_id');
        $rsm->addScalarResult('price_value', 'price_value');
        $rsm->addScalarResult('value', 'value');
        $rsm->addScalarResult('r','r');
        $rsm->addScalarResult('u','unit');
        $query = $em->createNativeQuery($query,$rsm);
        $rawResult = $query->getResult();

        // Kosztorys::KonwersjaDomyslnejTabeliZRepository($rawResult);
        return $rawResult;
       

    }
    
}
