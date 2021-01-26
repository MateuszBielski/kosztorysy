<?php

namespace App\Repository;

use App\Entity\Catalog;
use App\Entity\Circulation\CirculationNameAndUnit;
use App\Entity\Circulation\Material;
use App\Entity\Circulation\Material_N_U;
use App\Entity\ClTable;
use App\Entity\Chapter;
use App\Entity\TableRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

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

    public function findLoadingFieldsSeparately($id)
    {
        // $rsmTr = new ResultSetMapping();
        // $rsmTr->addEntityResult(TableRow::class, 'tr');
        // $rsmTr->addFieldResult('tr','sub_descripion','sub_descripion');
        // $rsm->addScalarResult('name_and_unit_id', 'nau_id');
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT 
        tr.id,
        tr.myNumber, 
        tr.subDescription, 
        ct.mainDescription, 
        ct.myNumber as ct_myNumber ,
        cp.name as cp_name,
        cat.name as cat_name
        FROM App\Entity\TableRow tr 
        INNER JOIN App\Entity\ClTable ct WITH tr.myTable = ct 
        INNER JOIN App\Entity\Chapter cp WITH ct.myChapter = cp
        INNER JOIN App\Entity\Catalog cat WITH cp.myCatalog = cat
        WHERE tr.id = $id");
        // $query->setParameter(1, $id);
        $result = $query->getResult()[0];
        $tableRow = new TableRow;
        $tableRow->setId($result['id']);
        $tableRow->setMyNumber($result['myNumber']);
        $tableRow->setSubDescription($result['subDescription']);

        $clTable = new ClTable;
        $clTable->setMyNumber($result['ct_myNumber']);
        $clTable->setMainDescription($result['mainDescription']);
        $tableRow->setMyTable($clTable);

        $chapter = new Chapter;
        $chapter->setName($result['cp_name']);
        $clTable->setMyChapter($chapter);

        $catalog = new Catalog;
        $catalog->setName($result['cat_name']);
        $chapter->setMyCatalog($catalog);

        $wypelnijNaklady = function ($rawResults,$klasa,$klasaNu,$dodajNaklad) use($tableRow)
        {
            foreach($rawResults as $row)
            {
                $naklady = new $klasa;
                $naklady->setValue($row['value']);
                $cnu = new $klasaNu;
                $cnu->setName($row['name']);
                $cnu->setUnit($row['unit']);
                $naklady->setNameAndUnit($cnu);
                $tableRow->$dodajNaklad($naklady);
            }
        };
        
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('value', 'value');
        $rsm->addScalarResult('name','name');
        $rsm->addScalarResult('unit','unit');
        $query = $em->createNativeQuery("select c.value,cnu.name,cnu.unit  from material m join circulation c on m.id = c.id join circulation_name_and_unit cnu on c.name_and_unit_id = cnu.id where table_row_id = $id",$rsm);
        ;
        
        $wypelnijNaklady($query->getResult(),Material::class,Material_N_U::class,'addMaterial');
        
        
        return $tableRow;
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
