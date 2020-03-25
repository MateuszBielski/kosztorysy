<?php

namespace App\Entity;

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
}
