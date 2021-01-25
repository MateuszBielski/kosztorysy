<?php

namespace App\Repository;

use App\Entity\Circulation\CirculationNameAndUnit;
use App\Entity\Circulation\Material;
use App\Entity\Circulation\Material_N_U;
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

    public function findLoadingMaterials($id)
    {
        // $rsmTr = new ResultSetMapping();
        // $rsmTr->addEntityResult(TableRow::class, 'tr');
        // $rsmTr->addFieldResult('tr','sub_descripion','sub_descripion');
        // $rsm->addScalarResult('name_and_unit_id', 'nau_id');
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT tr.myNumber, tr.subDescription FROM App\Entity\TableRow tr WHERE tr.id = $id");
        // $query->setParameter(1, $id);
        $result = $query->getResult()[0];
        $tableRow = new TableRow;
        $tableRow->setMyNumber($result['myNumber']);
        $tableRow->setSubDescription($result['subDescription']);
        
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('value', 'value');
        $rsm->addScalarResult('name','name');
        $rsm->addScalarResult('unit','unit');
        $query = $em->createNativeQuery("select c.value,cnu.name,cnu.unit  from material m join circulation c on m.id = c.id join circulation_name_and_unit cnu on c.name_and_unit_id = cnu.id where table_row_id = $id",$rsm);
        $rawResults = $query->getResult();
        foreach($rawResults as $row)
        {
            $material = new Material;
            $material->setValue($row['value']);
            $cnu = new Material_N_U;
            $cnu->setName($row['name']);
            $cnu->setUnit($row['unit']);
            $material->setNameAndUnit($cnu);
            $tableRow->addMaterial($material);
        }
        
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
