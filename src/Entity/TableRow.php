<?php

namespace App\Entity;

use App\Entity\Circulation\Equipment;
use App\Entity\Circulation\Labor;
use App\Entity\Circulation\Material;
use App\Service\Functions;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TableRowRepository")
 */
class TableRow
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ClTable", inversedBy="tableRows", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $myTable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    private $compoundDescription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subDescription;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Circulation\Labor", mappedBy="tableRow", orphanRemoval=true, cascade={"persist"}, fetch="EAGER")
     */
    protected $labors;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Circulation\Material", mappedBy="tableRow", orphanRemoval=true, cascade={"persist"}, fetch="EAGER")
     */
    protected $materials;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Circulation\Equipment", mappedBy="tableRow", orphanRemoval=true, cascade={"persist"}, fetch="EAGER")
     */
    protected $equipments;

    private $subIndices;

    private $laborsArray;
    private $materialsArray;
    private $equipmentsArray;
    private $optimized = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $myNumber;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $unit;

    public function __construct()
    {
        $this->labors = new ArrayCollection();
        $this->materials = new ArrayCollection();
        $this->equipments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMyTable(): ?ClTable
    {
        return $this->myTable;
    }

    public function setMyTable(?ClTable $myTable): self
    {
        $this->myTable = $myTable;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
    public function SelectNameAndUnitToCirculations($catalogCirculationN_U)
    {
        $selectFor = function ($circArray, $circLetter) use (&$catalogCirculationN_U) {
            foreach ($circArray as $circ) {
                $ind = $circ->getReadNameIndex();
                if (array_key_exists($ind, $catalogCirculationN_U[$circLetter]))
                    $circ->setNameAndUnit($catalogCirculationN_U[$circLetter][$ind]);
                else {
                    //problem pojawiał się rzadko w robociźnie dlatego przyjmiemy pierwszą grupę robotników istniejącą:
                    $circ->setNameAndUnit(reset($catalogCirculationN_U[$circLetter]));

                    // $table = $this->myTable;
                    // $chapter = $table->getMyChapter();
                    // $catalog = $chapter->getMyCatalog();
                    // echo "\n"."Problem z indeksami {$circLetter} w {$catalog->getName()} {$chapter->getName()} {$table->getMyNumber()} {$this->myNumber}";
                }
            }
        };
        $selectFor($this->getLabors(), 'R');
        $selectFor($this->getMaterials(), 'M');
        $selectFor($this->getEquipments(), 'S');
    }
    public function setValuesToCirculations(array $values)
    {
        $cR = count($this->getLabors());
        $cM = count($this->getMaterials());
        $cS = count($this->getEquipments());
        $isToMuchRMS = $cR + $cM + $cS - count($values);

        if ($isToMuchRMS > 0) {
            // echo "\nProblem w: ".$this->name." tabl ".$numTable." wiersz ".$numRow;
            // echo ' liczba wartości dla RMS '.count($tableRowValues);
            // echo ', R '.$cR.', M '.$cM.', S '.$cS;
            for ($i = 0; $i < $isToMuchRMS; $i++) $values[] = 0.0;
            // $numTableRow++;
            // continue;
        }
        $numTrV = 0;
        foreach ($this->getLabors() as $R) $R->setValue($values[$numTrV++]);
        foreach ($this->getMaterials() as $M) $M->setValue($values[$numTrV++]);
        foreach ($this->getEquipments() as $S) $S->setValue($values[$numTrV++]);
        $this->RemoveZeroValueCirculations();
    }
    public function RemoveZeroValueCirculations()
    {
        $zeroArray = function ($circ) {
            foreach ($circ as $key => $c) {
                if ($c->getValue() == 0) unset($c);
            }
        };
        $zeroArrayCollection = function ($circ) {
            foreach ($circ as $c) {
                if ($c->getValue() == 0) $circ->removeElement($c);
            }
        };
        $zero = $this->optimized ? $zeroArray : $zeroArrayCollection;
        $zero($this->getLabors());
        $zero($this->getMaterials());
        $zero($this->getEquipments());
    }
    public function SetAfterSplitLineIntoDescriptionAndIndices($subLine)
    {
        $slicePos = Functions::FindSlicePosition($subLine, '$', 7);
        $this->subDescription = trim(substr($subLine, 0, $slicePos - 1));
        $this->subIndices = trim(substr($subLine, $slicePos, strlen($subLine) - 1 - $slicePos));
    }
    public function createCompoundDescription($mainLine, $subLine)
    {
        $mainArray = explode('$', $mainLine);
        $subArray = explode('$', $subLine);
        $res = "";
        for ($i = 2; $i < 6; $i++) $res .= str_replace('^', $mainArray[$i], $subArray[$i]);
        $this->compoundDescription = trim($res);
        $this->myNumber = $this->ExtractMyNumber(end($subArray));
    }
    public function createCompoundRMSindices($mainIndices, $subIndices)
    {
        $this->CompoundIndices(
            $mainIndices,
            $subIndices,
            $this->labors,
            $this->materials,
            $this->equipments
        );
    }
    public function createCompoundRMSindices_optimized($mainIndices, $subIndices)
    {
        $this->optimized = true;
        $this->labors = null;
        $this->materials = null;
        $this->equipments = null;
        $this->laborsArray = array();
        $this->materialsArray = array();
        $this->equipmentsArray = array();
        $this->CompoundIndices(
            $mainIndices,
            $subIndices,
            $this->laborsArray,
            $this->materialsArray,
            $this->equipmentsArray
        );
    }
    private function CompoundIndices($mainIndices, $subIndices, &$labors, &$materials, &$equipments)
    {
        $mainArray = explode('$', $mainIndices);
        $subArray = explode('$', $subIndices);
        $arrayToReadNameIndices = count($subArray) > 4 ? $subArray : $mainArray;
        $posReadCircIndex = 4;
        $this->unit = trim($arrayToReadNameIndices[0]);
        //ważna jest referencja dla &$arrCirc - inaczej zwykła array nie chce działać
        $readAndSetCirc = function ($ind, $circClass, &$arrCirc) use (&$arrayToReadNameIndices, &$posReadCircIndex) {
            $numCirc = $arrayToReadNameIndices[$ind];
            for ($i = 0; $i < $numCirc; $i++) {
                $circClass = new $circClass;
                $circClass->setTableRow($this);
                $indexCircN_U = $arrayToReadNameIndices[$posReadCircIndex];
                $circClass->setReadNameIndex($indexCircN_U);
                $arrCirc[] = $circClass; // ta linijka powoduje gigantyczny nakład, ponieważ to jest ArrayCollection 
                //użycie zwykłej array znacznie przyspieszyłoby proces - tak myślałem dopóki nie sprawdziłem
                $posReadCircIndex++;
            }
        };
        $readAndSetCirc(1, Labor::class, $labors);
        $readAndSetCirc(2, Material::class, $materials);
        $readAndSetCirc(3, Equipment::class, $equipments);
    }
    public function getCompoundDescription()
    {
        return $this->compoundDescription;
    }
    public function CompoundDescription()
    {

        $this->createCompoundDescription($this->myTable->getMainDescription(), $this->subDescription);
        return $this->compoundDescription;
    }

    public function getSubDescription(): ?string
    {
        return $this->subDescription;
    }

    public function setSubDescription(string $subDescription): self
    {
        $this->subDescription = $subDescription;

        return $this;
    }

    /**
     * @return Collection|Labor[]
     */
    public function getLabors() //: Collection
    {
        return $this->optimized ? $this->laborsArray : $this->labors;
    }

    public function addLabor(Labor $labor): self
    {
        if (!$this->labors->contains($labor)) {
            $this->labors[] = $labor;
            $labor->setTableRow($this);
        }

        return $this;
    }

    public function removeLabor(Labor $labor): self
    {
        if ($this->labors->contains($labor)) {
            $this->labors->removeElement($labor);
            // set the owning side to null (unless already changed)
            if ($labor->getTableRow() === $this) {
                $labor->setTableRow(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Material[]
     */
    public function getMaterials() //: Collection
    {
        return $this->optimized ? $this->materialsArray : $this->materials;
    }

    public function addMaterial(Material $material): self
    {
        if (!$this->materials->contains($material)) {
            $this->materials[] = $material;
            $material->setTableRow($this);
        }

        return $this;
    }

    public function removeMaterial(Material $material): self
    {
        if ($this->materials->contains($material)) {
            $this->materials->removeElement($material);
            // set the owning side to null (unless already changed)
            if ($material->getTableRow() === $this) {
                $material->setTableRow(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Equipment[]
     */
    public function getEquipments() //: Collection
    {
        return $this->optimized ? $this->equipmentsArray : $this->equipments;
    }

    public function addEquipment(Equipment $equipment): self
    {
        if (!$this->equipments->contains($equipment)) {
            $this->equipments[] = $equipment;
            $equipment->setTableRow($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        if ($this->equipment->contains($equipment)) {
            $this->equipment->removeElement($equipment);
            // set the owning side to null (unless already changed)
            if ($equipment->getTableRow() === $this) {
                $equipment->setTableRow(null);
            }
        }

        return $this;
    }
    public function getCirculations()
    {
        $res = array();
        foreach ($this->getLabors() as $r) $res[] = $r;
        foreach ($this->getMaterials() as $m) $res[] = $m;
        foreach ($this->getEquipments() as $s) $res[] = $s;
        return $res;
    }
    public function getSubIndices()
    {
        return $this->subIndices;
    }
    public function getTotalLaborValue()
    {
        $res = 0.0;
        foreach ($this->getLabors() as $lab) $res += $lab->getValue();
        return $res;
    }
    public function setOptimized()
    {
        $this->optimized = true;
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
    public function ExtractMyNumber(string $text)
    {
        return intval(trim($text, " -"));
    }
    public function getFullName()
    {
        // return sprintf("%02d", $this->myNumber);
        return $this->myTable->getFullName() . '-' . sprintf("%02d", $this->myNumber);
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function GenerateValuesForTwigCostTable()
    {
        $valuesForTwig = array();
        $fillArray = function ($groupName, $circulations) use (&$valuesForTwig) {
            $row = array();
            $row[] = $groupName;
            $valuesForTwig[] = $row;
            foreach ($circulations as $c) {
                $row = array();
                $row[] = $c->getName();
                $row[] = $c->getValue();
                $row[] = $c->getUnit();
                $valuesForTwig[] = $row;
            }
        };
        $fillArray('--R--', $this->labors);
        $fillArray('--M--', $this->materials);
        $fillArray('--S--', $this->equipments);
        // echo "TableRow GenerateValuesForTwigCostTable";
        return $valuesForTwig;
    }
}
