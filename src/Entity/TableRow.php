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
     * @ORM\ManyToOne(targetEntity="App\Entity\Table", inversedBy="tableRows")
     * @ORM\JoinColumn(nullable=false)
     */
    private $myTable;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    private $compoundDescription;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subDescription;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Circulation\Labor", mappedBy="tableRow", orphanRemoval=true)
     */
    private $labors;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Circulation\Material", mappedBy="tableRow", orphanRemoval=true)
     */
    private $materials;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Circulation\Equipment", mappedBy="tableRow", orphanRemoval=true)
     */
    private $equipments;

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

    public function getMyTable(): ?Table
    {
        return $this->myTable;
    }

    public function setMyTable(?Table $myTable): self
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
    public function createCompoundDescriptionAndRMS($mainLine,$subLine)
    {
        
        $mainLine = Functions::ReplaceCharsAccordingUtf8($mainLine);
        $subLine = Functions::ReplaceCharsAccordingUtf8($subLine);
        $mainArray = explode('$',$mainLine);
        $subArray = explode('$',$subLine);
        $res = "";
        for($i = 2; $i < 6; $i++){
            // echo "\n".$mainArray[$i]." ".$subArray[$i+1];
            $res .= str_replace('^',$mainArray[$i],$subArray[$i]);
            // $res .=$mainArray[$i]." ".$subArray[$i+1]." ";
        }
        $numR = array_key_exists(8,$subArray) ? $subArray[8] : $mainArray[8];
        $numM = array_key_exists(9,$subArray) ? $subArray[9] : $mainArray[9];
        $numS = array_key_exists(10,$subArray) ? $subArray[10] : $mainArray[10];

        for($i = 0 ; $i < $numR ; $i++)$this->labors[] = new Labor;
        for($i = 0 ; $i < $numM ; $i++)$this->materials[] = new Material;
        for($i = 0 ; $i < $numS ; $i++)$this->equipments[] = new Equipment;
        $this->compoundDescription = trim($res);
    }
    public function getCompoundDescription()
    {
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
}
