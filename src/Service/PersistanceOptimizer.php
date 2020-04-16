<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class PersistanceOptimizer
{
   private $em;
   private $conn;
   private $db_name;

   private $catalogs;
   private $chapters;
   private $tables;
   private $tableRows;
   private $labors;
   private $materials;
   private $equipments;
   private $catalogsParentId;
   private $chaptersParentId;
   private $tablesParentId;
   private $tableRowsParentId;
   private $laborsParentId;
   private $materialsParentId;
   private $equipmentsParentId;

   private $query;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->conn = $entityManager->getConnection();
        $this->db_name = $this->conn->getDatabase();
        
    }
    public function getCatalogs()
    {   
        return $this->catalogs;
    }
    public function getChapters()
    {       
        return $this->chapters;
    }
    public function getTables()
    {
        return $this->tables;
    }
    public function getTableRows()
    {
        return $this->tableRows;
    }
    public function getLabors()
    {
        return $this->labors;
    }
    public function getMaterials()
    {
        return $this->materials;
    }
    public function getEquipments()
    {
        return $this->equipments;
    }
    public function getQuery()
    {
        return $this->query;
    }
    public function RetirieveLastAutoIncFor(string $tableName)
    {
        $stmt = $this->conn->executeQuery('select auto_increment from information_schema.TABLES where table_schema = \''.$this->db_name.'\' and table_name = \''.$tableName.'\'');
        $arrResult = $stmt->fetch();
        return $arrResult['auto_increment'];
    }
    public function Aggregate(array $catalogs)
    {
        $this->catalogs = array();
        $this->chapters = array();
        $this->tables = array();
        $this->tableRows = array();
        $this->labors = array();
        $this->materials = array();
        $this->equipments = array();
        $this->catalogsParentId = array();
        $this->chaptersParentId = array();
        $this->tablesParentId = array();
        $this->tableRowsParentId = array();
        $this->laborsParentId = array();
        $this->materialsParentId = array();
        $this->equipmentsParentId = array();
        // $this->catalogs = $catalogs;
        $catalogId = $this->RetirieveLastAutoIncFor('catalog');
        $chapterId = $this->RetirieveLastAutoIncFor('chapter');
        $tableId = $this->RetirieveLastAutoIncFor('cl_table');
        $tableRowId = $this->RetirieveLastAutoIncFor('table_row');
        foreach($catalogs as $cat)
        {
            // $cat->setId($catalogId++);
            $this->catalogs[$catalogId] = $cat;
            foreach($cat->getMyChapters() as $chap)
            {
                $this->chapters[$chapterId] =  $chap;
                $this->chaptersParentId[$chapterId] = $catalogId;
                foreach ($chap->getTables() as $tab) {
                    $this->tables[$tableId] = $tab;
                    $this->tablesParentId[$tableId] = $chapterId;
                    foreach ($tab->getTableRows() as $tr)
                    {
                        $this->tableRows[$tableRowId] = $tr;
                        $this->tableRowsParentId[$tableRowId] = $tableId;
                        foreach($tr->getLabors() as $R){$this->labors[] = $R;};
                        foreach($tr->getMaterials() as $M){$this->materials[] = $M;};
                        foreach($tr->getEquipments() as $S){$this->equipments[] = $S;};
                        $tableRowId++;
                    }
                    $tableId++;
                }
                $chapterId++;
            } 
            $catalogId++;
        }
    }
    public function persist()
    {
        $query = 'insert into catalog values ';
        foreach($this->catalogs as $id => $cat)
        {
            $query .='('.$id.',\''.$cat->getName().'\'),';
        }
        $query = rtrim($query,",");
        if (count($this->chapters) > 0)
        {
            $query .= '; insert into chapter values ';
            foreach($this->chapters as $id => $chap)
            {
                $query .='('.$id.','.$this->chaptersParentId[$id].',\''.$chap->getName().'\',\''.$chap->getDescription().'\'),';
            }
            // echo "\n".$queryInsertChapter;
            $query = rtrim($query,",");
        } 

        if (count($this->tables) > 0)
        {
            $query .= '; insert into cl_table values ';
            foreach( $this->tables as $id => $tab)
            {
                $query .='('.$id.','.$this->tablesParentId[$id].',\''.$tab->getMainDescription().'\'),';//."\n"
            }
            $query = rtrim($query,",");
        }

        if (count($this->tableRows) > 0) 
        {
            $query .= '; insert into table_row (id,my_table_id,sub_description) values ';
            foreach( $this->tableRows as $id => $tr)
            {
                $query .='('.$id.','.$this->tableRowsParentId[$id].',\''.$tr->getSubDescription().'\'),';//."\n"
            }
            $query = rtrim($query,",");
        }
        // echo "\nDługość zapytania: ".strlen($query);
        // $this->query = $query;
        $this->conn->executeQuery($query);
    }
}