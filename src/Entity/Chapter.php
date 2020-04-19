<?php

namespace App\Entity;

use App\Service\Functions;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChapterRepository")
 */
class Chapter
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $name;

   
    private $number;//czy potrzebne?

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Catalog", inversedBy="myChapters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $myCatalog = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ClTable", mappedBy="myChapter", orphanRemoval=true, cascade={"persist"})
     */
    private $tables;

    private $myDetailsFileBaseName;
    private $circValues = array();
    // private $myCatalogId= null;

    public function __construct()
    {
        $this->tables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getMyCatalog(): ?Catalog
    {
        return $this->myCatalog;
    }

    public function setMyCatalog(?Catalog $myCatalog): self
    {
        $this->myCatalog = $myCatalog;

        return $this;
    }
   

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|ClTable[]
     */
    public function getTables(): Collection
    {
        return $this->tables;
    }

    public function addTable(ClTable $table): self
    {
        if (!$this->tables->contains($table)) {
            $this->tables[] = $table;
            $table->setMyChapter($this);
        }

        return $this;
    }

    public function removeTable(ClTable $table): self
    {
        if ($this->tables->contains($table)) {
            $this->tables->removeElement($table);
            // set the owning side to null (unless already changed)
            if ($table->getMyChapter() === $this) {
                $table->setMyChapter(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of myDetailsFileBaseName
     */ 
    public function getMyDetailsFileBaseName()
    {
        return $this->myDetailsFileBaseName;
    }

    /**
     * Set the value of myDetailsFileBaseName
     *
     * @return  self
     */ 
    public function setMyDetailsFileBaseName($myDetailsFileBaseName)
    {
        $this->myDetailsFileBaseName = $myDetailsFileBaseName;

        return $this;
    }
    
    public function readFrom($line,$readLevel = false)
    {
        $line = Functions::ReplaceCharsAccordingUtf8($line);
        $fields = explode('$',$line );
        $start = strpos($fields[1],'(') + 1;
        $stop = strpos($fields[1],')');
        $this->name = trim(substr($fields[1],$start,$stop-$start));
        $start = strpos($fields[1],'*') + 1;
        $this->description = trim(substr($fields[1],$start));
        // $pathToFileOP = $this->myCatalog->dirPath;
        if($this->myCatalog)
        {
            $dirPath = $this->myCatalog->dirPath;
            $baseName = trim($fields[3]);
            $ext = 'OP';
            $opFileName = $dirPath.'/'.$baseName.'.'.$ext;
            $opFile = @fopen($opFileName,'r');
            if(!$opFile) $opFile = Functions::FindFileInDirAndOpen($dirPath,$baseName,$ext);
            if($opFile && (TABLE_DIST & $readLevel))$this->LoadTablesWithDescriptionFromOP($opFile,$readLevel);
            fclose($opFile);
            $ext = 'NOR';
            $norFileName = $dirPath.'/'.$baseName.'.'.$ext;
            $norFile = @fopen($norFileName,'r');
            if(!$norFile) $norFile = Functions::FindFileInDirAndOpen($dirPath,$baseName,$ext);
            if($norFile) $this->LoadCircValuesFromNOR($norFile);
            fclose($norFile);
            try
            {
                if(DESCRIPaRMS_DIST & $readLevel)$this->GiveValuesToCirculations();
            }catch(\Exception $e){
                echo "\n".$e->getMessage();
            }

        }

        
    }
    public function LoadTablesWithDescriptionFromOP($detailFile,$readLevel = false)
    {
        fseek($detailFile,0);
        //pierwsza linia niepotrzebna
        fgets($detailFile);
        $numLine = 2;
        $numTable = 0;
        $tablesBeginLine = array();
        $tablesBeginLine[0] = INF;
        $tablesNumRow = array();

        while($numLine < $tablesBeginLine[0]){
            $line = Functions::ReplaceCharsAccordingUtf8(fgets($detailFile));
            $posDelim0 = strpos($line,'$');
            $ptrToTableDetail = substr($line,0,$posDelim0);
            $posDelim0 = strpos($line,'.') + 1;
            $posDelim1 = strpos($line,'$',$posDelim0);
            $len = $posDelim1 - $posDelim0;
            $tablesNumRow[$numTable] = substr($line,$posDelim0,$len);
            // echo "\n".$tablesNumRow[$numTable];
            $tablesBeginLine[$numTable] = $ptrToTableDetail;
            $table = new ClTable;
            $table->setMyChapter($this);
            // $table->setMainDescription($line);
            $table->SetAfterSplitLineIntoDescriptionAndIndices($line);
            $this->tables[] = $table;
            $numLine++;
            $numTable++;
        }
        $totalTableCount = $numTable;
        $numTable = 0;
        if (!(TABLE_ROW_DIST & $readLevel))return;
        while($numTable < $totalTableCount){
            
            $table = $this->tables[$numTable];
            $mainDescription = $table->getMainDescription();
            $mainIndices = $table->getMainIndices();
            $numRow = 0;
            //linia tytułowa tablicy
            fgets($detailFile);
            while($numRow < $tablesNumRow[$numTable]){
                $subLine = Functions::ReplaceCharsAccordingUtf8(fgets($detailFile));
                $tableRow = new TableRow;
                $tableRow->setMyTable($table);
                if(DESCRIPaRMS_DIST & $readLevel){
                    $tableRow->SetAfterSplitLineIntoDescriptionAndIndices($subLine);
                    $tableRow->createCompoundDescription($mainDescription,$tableRow->getSubDescription());
                    $createIndices =  (OPTIMIZE_TR & $readLevel) ? "createCompoundRMSindices_optimized" : "createCompoundRMSindices";
                    $tableRow->$createIndices($mainIndices,$tableRow->getSubIndices());
                }
                $table->getTableRows()[] = $tableRow;
                $numLine++;
                $numRow++;
            }
            $thisTable = true;
            $numTable++;
        }
    }

    public function LoadCircValuesFromNOR($norFile)
    {
        
        while($numValues = fgets($norFile)){
            $i = 0;
            $arr = array();
            while($i < $numValues){
                $arr[] = floatval(fgets($norFile));
                $i++;
            }
            $this->circValues[] = $arr;
        }
    }
    public function getCircValues()
    {
        return $this->circValues;
    }
    public function GiveValuesToCirculations()
    {
        $idName = $this->myCatalog ? "{$this->myCatalog->getName()}" : "";
        $idName .= " {$this->name}";
        if (!count($this->circValues)) throw new \Exception('brak wartości nakładów'.$idName);
        if (!count($this->tables)) throw new \Exception('Nie wczytane tabele'.$idName);
        $totalNumberOfTableRows = 0;
        foreach($this->tables as $table)
        {
            $totalNumberOfTableRows += count($table->getTableRows());
        }
        if ($totalNumberOfTableRows > count($this->circValues)) throw new \Exception('nie prawidłowa ilość wartości w NOR '.$idName);
        $numTableRow = 0;
        $numTable = 0;
        foreach($this->tables as $table)
        {
            // $numRow = 0;
            foreach($table->getTableRows() as $tr)
            {
                // $tableRowValues = $this->circValues[$numTableRow];
                $tr->setValuesToCirculations($this->circValues[$numTableRow]);
                // $cR = count($tr->getLabors());
                // $cM = count($tr->getMaterials());
                // $cS = count($tr->getEquipments());
                // $isToMuchRMS = $cR + $cM + $cS - count($tableRowValues);

                // if ($isToMuchRMS > 0)
                // {
                //     // echo "\nProblem w: ".$this->name." tabl ".$numTable." wiersz ".$numRow;
                //     // echo ' liczba wartości dla RMS '.count($tableRowValues);
                //     // echo ', R '.$cR.', M '.$cM.', S '.$cS;
                //     for($i = 0; $i < $isToMuchRMS;$i++)$tableRowValues[] = 0.0;
                //     // $numTableRow++;
                //     // continue;
                // }
                // $numTrV = 0;
                // foreach($tr->getLabors() as $R) $R->setValue($tableRowValues[$numTrV++]);
                // foreach($tr->getMaterials() as $M) $M->setValue($tableRowValues[$numTrV++]);
                // foreach($tr->getEquipments() as $S) $S->setValue($tableRowValues[$numTrV++]);

                $numTableRow++;
                // $numRow++;
            }
            $numTable++;
        }
    }
    
}
