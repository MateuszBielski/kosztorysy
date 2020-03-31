<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TableRepository")
 */
class Table
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Chapter", inversedBy="tables")
     * @ORM\JoinColumn(nullable=false)
     */
    private $myChapter;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TableRow", mappedBy="myTable", orphanRemoval=true)
     */
    private $tableRows;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mainDescription;

    public function __construct()
    {
        $this->tableRows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
}
