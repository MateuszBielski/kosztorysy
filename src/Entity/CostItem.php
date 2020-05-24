<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CostItemRepository")
 */
class CostItem extends TableRow
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $survey;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getSurvey(): ?float
    {
        return $this->survey;
    }

    public function setSurvey(?float $survey): self
    {
        $this->survey = $survey;

        return $this;
    }

    public function Initialize(TableRow $tr)
    {
        $this->myTable = $tr->getMyTable();
        $this->myNumber = $tr->getMyNumber();
        $this->labors = ItemPrice::FactoryFromCirculations($tr->getLabors());
        $this->materials = ItemPrice::FactoryFromCirculations($tr->getMaterials());
        $this->equipments = ItemPrice::FactoryFromCirculations($tr->getEquipments());
    }
    public function GenerateValuesForTwigCostTable()
    {
        $valuesForTwig = array();
        $fillArray = function ($groupName, $circulations) use (&$valuesForTwig) {
            $row = array();
            $row[] = '';
            $row[] = $groupName;
            $valuesForTwig[] = $row;
            foreach ($circulations as $c) {
                $row = array();
                $row[] = $c->getPriceValue();
                $row[] = $c->getValue();
                $valuesForTwig[$c->getNameAndUnit()->getId()] = $row;
            }
        };
        $fillArray('--R--', $this->labors);
        $fillArray('--M--', $this->materials);
        $fillArray('--S--', $this->equipments);
        // echo "TableRow GenerateValuesForTwigCostTable";
        return $valuesForTwig;
    }
}
