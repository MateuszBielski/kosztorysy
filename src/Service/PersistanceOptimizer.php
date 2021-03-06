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
   private $uniqueCirculations;

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
        $circulationId = $this->RetirieveLastAutoIncFor('circulation');
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
                        foreach($tr->getLabors() as $R)
                        {
                            $this->labors[$circulationId] = $R;
                            $this->laborsParentId[$circulationId] = $tableRowId;
                            $circulationId++;
                        }
                        foreach($tr->getMaterials() as $M)
                        {
                            $this->materials[$circulationId] = $M;
                            $this->materialsParentId[$circulationId] = $tableRowId;
                            $circulationId++;
                        }
                        foreach($tr->getEquipments() as $S)
                        {
                            $this->equipments[$circulationId] = $S;
                            $this->equipmentsParentId[$circulationId] = $tableRowId;
                            $circulationId++;
                        }
                        $tableRowId++;
                    }
                    $tableId++;
                }
                $chapterId++;
            } 
            $catalogId++;
        }
    }
    public function setUniqueCirculations(array $uniqueCirculations)
    {
        $this->uniqueCirculations = $uniqueCirculations;
    }
    private function GenerateSqlQueryLongLines(): string
    {
        $query = '';
        if(is_array($this->uniqueCirculations) && count($this->uniqueCirculations))
        {
            $query .= 'insert into circulation_name_and_unit (id,name,unit,eto,discriminator) values ';
            $circulationsNUseparated = array();
            foreach($this->uniqueCirculations as $cir)
            {
                $query .= $cir->GenerateQueryToInsert();
                $cir->AddSelfToCorrectSubArray($circulationsNUseparated);
            }
            $query = rtrim($query,",");
            if (count($circulationsNUseparated['R']))
            {
                $query .= '; insert into labor_n_u values ';
                foreach($circulationsNUseparated['R'] as $Rnu)$query .= "({$Rnu->getId()}),";
                $query = rtrim($query,",");
            }
            if (count($circulationsNUseparated['M']))
            {
                $query .= '; insert into material_n_u values ';
                foreach($circulationsNUseparated['M'] as $Mnu)$query .= "({$Mnu->getId()}),";
                $query = rtrim($query,",");
            }
            if (count($circulationsNUseparated['S']))
            {
                $query .= '; insert into equipment_n_u values ';
                foreach($circulationsNUseparated['S'] as $Snu)$query .= "({$Snu->getId()}),";
                $query = rtrim($query,",");
            }
            $query .= '; ';
        }
        $query .= 'insert into catalog values ';
        foreach($this->catalogs as $id => $cat)
        {
            // $query .='('.$id.',\''.$cat->getName().'\'),';
            $query .='('.$id.',\''.$cat->getName().'\',\''.$cat->getDescription().'\'),';
        }
        $query = rtrim($query,",");
        if (count($this->chapters) > 0)
        {
            $query .= '; insert into chapter values ';
            foreach($this->chapters as $id => $chap)
            {
                $query .='('.$id.','.$this->chaptersParentId[$id].',\''.$chap->getName().'\',\''.$chap->getDescription().'\'),';//."\n"
            }
            $query = rtrim($query,",");
        } 

        if (count($this->tables) > 0)
        {
            $query .= '; insert into cl_table values ';
            foreach( $this->tables as $id => $tab)
            {
                $query .='('.$id.','.$this->tablesParentId[$id].',\''.$tab->getMainDescription().'\','.$tab->getMyNumber().'),';//."\n"
            }
            $query = rtrim($query,",");
        }

        if (count($this->tableRows) > 0) 
        {
            $query .= '; insert into table_row (id,my_table_id,sub_description,my_number,unit) values ';
            foreach( $this->tableRows as $id => $tr)
            {
                $query .='('.$id.','.$this->tableRowsParentId[$id].',\''.$tr->getSubDescription().'\','.$tr->getMyNumber().',\''.$tr->getUnit().'\'),';//."\n"
            }
            $query = rtrim($query,",");
        }
        if (count($this->labors) || count($this->materials) || count($this->equipments))
        {
            $query .= '; insert into circulation (id,name_and_unit_id,value,discriminator,group_number) values ';
            foreach ($this->labors as $id => $circ) $query .= "($id,{$circ->getNameAndUnit()->getId()},{$circ->getValue()},'labor',{$circ->getGroupNumber()}),";
            foreach ($this->materials as $id => $circ) $query .= "($id,{$circ->getNameAndUnit()->getId()},{$circ->getValue()},'material',{$circ->getGroupNumber()}),";
            foreach ($this->equipments as $id => $circ) $query .= "($id,{$circ->getNameAndUnit()->getId()},{$circ->getValue()},'equipment',{$circ->getGroupNumber()}),";
            $query = rtrim($query,",");

            $query .= '; insert into labor values ';
            foreach ($this->labors as $id => $lab) $query .="($id,{$this->laborsParentId[$id]}),";
            $query = rtrim($query,",");

            $query .= '; insert into material values ';
            foreach ($this->materials as $id => $mat) $query .="($id,{$this->materialsParentId[$id]}),";
            $query = rtrim($query,",");

            $query .= '; insert into equipment values ';
            foreach ($this->equipments as $id => $equ) $query .="($id,{$this->equipmentsParentId[$id]}),";
            $query = rtrim($query,",");
        }
        // echo "\nXX".strlen($query);
        return $query;
    }private function GenerateSqlQuery(): string
    {
        $query = 'insert into catalog values ';
        foreach($this->catalogs as $id => $cat)
        {
            // $query .='('.$id.',\''.$cat->getName().'\')';
            $query .='('.$id.',\''.$cat->getName().'\',\''.$cat->getDescription().'\'),';
        }
        $query = rtrim($query,",");
        if (count($this->chapters) > 0)
        {
            $query .= '; insert into chapter values ';
            foreach($this->chapters as $id => $chap)
            {
                $query .='('.$id.','.$this->chaptersParentId[$id].',\''.$chap->getName().'\',\''.$chap->getDescription().'\'),';//."\n"
            }
            $query = rtrim($query,",");
        } 

        if (count($this->tables) > 0)
        {
            // $query .= '; insert into cl_table values ';
            foreach( $this->tables as $id => $tab)
            {
                $query .='; insert into cl_table values ('.$id.','.$this->tablesParentId[$id].',\''.$tab->getMainDescription().'\','.$tab->getMyNumber().')';//."\n"
            }
            $query = rtrim($query,",");
        }

        if (count($this->tableRows) > 0) 
        {
            // $query .= '; insert into table_row (id,my_table_id,sub_description,my_number) values ';
            foreach( $this->tableRows as $id => $tr)
            {
                $query .='; insert into table_row (id,my_table_id,sub_description,my_number) values ('.$id.','.$this->tableRowsParentId[$id].',\''.$tr->getSubDescription().'\','.$tr->getMyNumber().')';//."\n"
            }
            $query = rtrim($query,",");
        }
        if (count($this->labors) || count($this->materials) || count($this->equipments))
        {
            $query .= '; insert into circulation (id,name_and_unit_id,value,discriminator,group_number) values ';
            foreach ($this->labors as $id => $circ) $query .= "($id,{$circ->getNameAndUnit()->getId()},{$circ->getValue()},'labor',{$circ->getGroupNumber()}),";
            foreach ($this->materials as $id => $circ) $query .= "($id,{$circ->getNameAndUnit()->getId()},{$circ->getValue()},'material',{$circ->getGroupNumber()}),";
            foreach ($this->equipments as $id => $circ) $query .= "($id,{$circ->getNameAndUnit()->getId()},{$circ->getValue()},'equipment',{$circ->getGroupNumber()}),";
            $query = rtrim($query,",");

            $query .= '; insert into labor values ';
            foreach ($this->labors as $id => $lab) $query .="($id,{$this->laborsParentId[$id]}),";
            $query = rtrim($query,",");

            $query .= '; insert into material values ';
            foreach ($this->materials as $id => $mat) $query .="($id,{$this->materialsParentId[$id]}),";
            $query = rtrim($query,",");

            $query .= '; insert into equipment values ';
            foreach ($this->equipments as $id => $equ) $query .="($id,{$this->equipmentsParentId[$id]}),";
            $query = rtrim($query,",");
        }
        return $query;
    }
    public function persist()
    {
        
        // echo "\nDługość zapytania: ".strlen($query);
        // $this->query = $query;
        $this->conn->executeQuery($this->GenerateSqlQueryLongLines());
    }

    public function GenerateSqlFile($baseName)
    {
        $fileName = $baseName.".sql";
        $fileToSave = fopen($fileName,'w');
        fwrite($fileToSave,$this->GenerateSqlQueryLongLines());
        fclose($fileToSave);
    }
   
}