<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Service\Functions;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TableRepository")
 */
class ClTable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Chapter", inversedBy="tables",fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $myChapter;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TableRow", mappedBy="myTable", orphanRemoval=true, cascade={"persist"},fetch="EXTRA_LAZY")
     */
    private $tableRows;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mainDescription;

    private $mainIndices;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $myNumber;

    public function __construct()
    {
        $this->tableRows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id)
    {
        $this->id = $id;
    }

    public function getMyChapter(): ?Chapter
    {
        return $this->myChapter;
    }

    public function setMyChapter(?Chapter $myChapter): self
    {
        $this->myChapter = $myChapter;

        return $this;
    }

    /**
     * @return Collection|TableRow[]
     */
    public function getTableRows(): Collection
    {
        return $this->tableRows;
    }

    public function addTableRow(TableRow $tableRow): self
    {
        if (!$this->tableRows->contains($tableRow)) {
            $this->tableRows[] = $tableRow;
            $tableRow->setMyTable($this);
        }

        return $this;
    }

    public function removeTableRow(TableRow $tableRow): self
    {
        if ($this->tableRows->contains($tableRow)) {
            $this->tableRows->removeElement($tableRow);
            // set the owning side to null (unless already changed)
            if ($tableRow->getMyTable() === $this) {
                $tableRow->setMyTable(null);
            }
        }

        return $this;
    }

    public function getMainDescription(): ?string
    {
        return $this->mainDescription;
    }

    public function setMainDescription(string $mainDescription): self
    {
        $this->mainDescription = $mainDescription;

        return $this;
    } 
    public function getMainIndices(): ?string
    {
        return $this->mainIndices;
    }

    public function setMainIndices(string $mainIndices): self
    {
        $this->mainIndices = $mainIndices;

        return $this;
    } 
    public function SetAfterSplitLineIntoDescriptionAndIndices($line)
    {
        $slicePos = Functions::FindSlicePosition($line,'$',7);
        $this->mainDescription = trim(substr($line,0,$slicePos - 1));
        $this->mainIndices = substr($line,$slicePos,strlen($line)-1-$slicePos);
    }

    public function getMyNumber(): ?int
    {
        return $this->myNumber;
    }

    public function setMyNumber(?int $myNumber): self
    {
        $this->myNumber = $myNumber;

        return $this;
    }
    public function ExtractMyNumber(string $textLine)
    {
        return intval(substr(trim($textLine," *"),9));
    }
    public function getFullName()
    {
        // return sprintf("%02d",$this->myNumber);
        return $this->myChapter->getFullName().sprintf("%02d",$this->myNumber);
    }
    public function getDescription()
    {
        $arr = explode('$',$this->mainDescription);
        array_shift($arr);
        array_shift($arr);
        array_pop($arr);
        return implode('',$arr);
    }

    public function CreateDependecyForRenderAndTest($param)
    {
        $getIfexists = function($parmName) use($param)
        {
            return array_key_exists($parmName,$param) ? $param[$parmName] : null;
        };


        $this->myNumber = $getIfexists('ct_myNumber');
        $this->mainDescription = $getIfexists('mainDescription');

        $chapter = new Chapter;
        $cp_name = $getIfexists('cp_name');
        if ($cp_name != null)
        $chapter->setName($cp_name);
        $this->myChapter = $chapter;

        $catalog = new Catalog;
        $cat_name = $getIfexists('cat_name');
        if ($cat_name != null)
        $catalog->setName($cat_name);
        $chapter->setMyCatalog($catalog);

    }
}
