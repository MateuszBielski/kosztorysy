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
     * @ORM\OneToMany(targetEntity="App\Entity\Table", mappedBy="myChapter", orphanRemoval=true, cascade={"persist"})
     */
    private $tables;

    private $myDetailsFileBaseName;
    private $circValues = array();
    private $refToDirPath = null;

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
     * @return Collection|Table[]
     */
    public function getTables(): Collection
    {
        return $this->tables;
    }

    public function addTable(Table $table): self
    {
        if (!$this->tables->contains($table)) {
            $this->tables[] = $table;
            $table->setMyChapter($this);
        }

        return $this;
    }

    public function removeTable(Table $table): self
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
            $line = fgets($detailFile);
            $posDelim0 = strpos($line,'$');
            $ptrToTableDetail = substr($line,0,$posDelim0);
            $posDelim0 = strpos($line,'.') + 1;
            $posDelim1 = strpos($line,'$',$posDelim0);
            $len = $posDelim1 - $posDelim0;
            $tablesNumRow[$numTable] = substr($line,$posDelim0,$len);
            // echo "\n".$tablesNumRow[$numTable];
            $tablesBeginLine[$numTable] = $ptrToTableDetail;
            $table = new Table;
            $table->setMyChapter($this);
            $table->setMainDescription($line);
            $this->tables[] = $table;
            $numLine++;
            $numTable++;
        }
        $totalTableCount = $numTable;
        // $read = true;
        $thisTable = true;
        $numTable = 0;
        if (!(TABLE_ROW_DIST & $readLevel))return;
        while($numTable < $totalTableCount){
            
            $table = $this->tables[$numTable];
            $mainLine = $table->getMainDescription();
            $numRow = 0;
            //linia tytułowa tablicy
            fgets($detailFile);
            while($numRow < $tablesNumRow[$numTable]){
                $subLine = fgets($detailFile);
                $tableRow = new TableRow;
                if(DESCRIPaRMS_DIST & $readLevel)
                $tableRow->createCompoundDescriptionAndRMS($mainLine,$subLine);
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

    
}
