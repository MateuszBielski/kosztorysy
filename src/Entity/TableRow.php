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
    private $myTable;

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
    private $labors;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Circulation\Material", mappedBy="tableRow", orphanRemoval=true, cascade={"persist"}, fetch="EAGER")
     */
    private $materials;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Circulation\Equipment", mappedBy="tableRow", orphanRemoval=true, cascade={"persist"}, fetch="EAGER")
     */
    private $equipments;

    private $subIndices;

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
    public function SetAfterSplitLineIntoDescriptionAndIndices($subLine)
    {
        $slicePos = Functions::FindSlicePosition($subLine,'$',7);
        $this->subDescription = trim(substr($subLine,0,$slicePos - 1));
        $this->subIndices = trim(substr($subLine,$slicePos,strlen($subLine)-1-$slicePos));
    }
    public function createCompoundDescription($mainLine,$subLine)
    {
        $mainArray = explode('$',$mainLine);
        $subArray = explode('$',$subLine);
        $res = "";
        for($i = 2; $i < 6; $i++) $res .= str_replace('^',$mainArray[$i],$subArray[$i]);
        $this->compoundDescription = trim($res);
    }
    public function createCompoundRMSindices($mainIndices,$subIndices)
    {
        $mainArray = explode('$',$mainIndices);
        $subArray = explode('$',$subIndices);
        $arrayToReadNameIndices = count($subArray) > 4 ? $subArray : $mainArray;
        $posReadCircIndex = 4;
        $readAndSetCirc = function($ind,$circClass,$arrCirc) use ($arrayToReadNameIndices,&$posReadCircIndex)
        {$numCirc = $arrayToReadNameIndices[$ind];
            for($i = 0 ; $i < $numCirc ; $i++){
                $circClass = new $circClass;
                $circClass->setTableRow($this);
                $circClass->setReadNameIndex($arrayToReadNameIndices[$posReadCircIndex]);
                $arrCirc[] = $circClass;// ta linijka powoduje gigantyczny nakład, ponieważ to jest ArrayCollection 
                //użycie zwykłej array znacznie przyspieszyłoby proces
                $posReadCircIndex++;

            }
        };
        $readAndSetCirc(1,Labor::class,$this->labors);
        $readAndSetCirc(2,Material::class,$this->materials);
        $readAndSetCirc(3,Equipment::class,$this->equipments);
    }
    public function getCompoundDescription()
    {
       return $this->compoundDescription;
    }
    public function CompoundDescription()
    {
        
        $this->createCompoundDescription($this->myTable->getMainDescription(),$this->subDescription);
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
    public function getLabors(): Collection
    {
        return $this->labors;
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
    public function getMaterials(): Collection
    {
        return $this->materials;
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
    public function getEquipments(): Collection
    {
        return $this->equipments;
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
    public function getSubIndices()
    {
        return $this->subIndices;
    }
    public function getTotalLaborValue()
    {
        $res = 0.0;
        foreach($this->labors as $lab) $res += $lab->getValue();
        return $res;
    }
}
