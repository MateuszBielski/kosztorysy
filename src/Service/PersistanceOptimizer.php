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
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->conn = $entityManager->getConnection();
        $this->db_name = $this->conn->getDatabase();
        $this->catalogs = array();
        $this->chapters = array();
        $this->tables = array();
        $this->tableRows = array();
    }

    public function RetirieveLastAutoIncFor(string $tableName)
    {
        $stmt = $this->conn->executeQuery('select auto_increment from information_schema.TABLES where table_schema = \''.$this->db_name.'\' and table_name = \''.$tableName.'\'');
        $arrResult = $stmt->fetch();
        return $arrResult['auto_increment'];
    }
    public function Aggregate(array $catalogs)
    {
        $this->catalogs = $catalogs;
        foreach($catalogs as $cat)
        {
            foreach($cat->getMyChapters() as $chap)
            {
                $this->chapters[] =  $chap;
                foreach ($chap->getTables() as $tab) {
                    $this->tables[] = $tab;
                    foreach ($tab->getTableRows() as $tr)
                    {
                        $this->tableRows[] = $tr;
                    }

                }
            } 
            
        }
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
}