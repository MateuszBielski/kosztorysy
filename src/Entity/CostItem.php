<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CostItemRepository")
 */
class CostItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TableRow")
     */
    private $tableRow;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $survey;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTableRow(): ?TableRow
    {
        return $this->tableRow;
    }

    public function setTableRow(?TableRow $tableRow): self
    {
        $this->tableRow = $tableRow;

        return $this;
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
    public function GenerateValuesForTwigCostTable()
    {
        $arrayForTwig = array();
        // for($i = 0; $i)
    }
}
