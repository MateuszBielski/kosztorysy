<?php

namespace App\Entity\Circulation;

use App\Entity\TableRow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Material extends Circulation
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TableRow", inversedBy="materials")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tableRow;

    public function getTableRow(): ?TableRow
    {
        return $this->tableRow;
    }

    public function setTableRow(?TableRow $tableRow): self
    {
        $this->tableRow = $tableRow;

        return $this;
    }
}