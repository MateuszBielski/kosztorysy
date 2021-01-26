<?php

namespace App\Repository;

use App\Entity\Catalog;
use App\Entity\Circulation\CirculationNameAndUnit;
use App\Entity\Circulation\Material;
use App\Entity\Circulation\Material_N_U;
use App\Entity\ClTable;
use App\Entity\Chapter;
use App\Entity\Circulation\Equipment;
use App\Entity\Circulation\Equipment_N_U;
use App\Entity\Circulation\Labor;
use App\Entity\Circulation\Labor_N_U;
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
        ct.id as ct_id, 
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
        
        $tableRow->CreateDependecyForRender($result);
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
        $wypelnijNaklady($query->getResult(),Material::class,Material_N_U::class,'addMaterial');
        
        $query = $em->createNativeQuery("select c.value,cnu.name,cnu.unit  from equipment e join circulation c on e.id = c.id join circulation_name_and_unit cnu on c.name_and_unit_id = cnu.id where table_row_id = $id",$rsm);
        $wypelnijNaklady($query->getResult(),Equipment::class,Equipment_N_U::class,'addEquipment');

        $query = $em->createNativeQuery("select c.value,cnu.name,cnu.unit  from labor l join circulation c on l.id = c.id join circulation_name_and_unit cnu on c.name_and_unit_id = cnu.id where table_row_id = $id",$rsm);
        $wypelnijNaklady($query->getResult(),Labor::class,Labor_N_U::class,'addLabor');

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
