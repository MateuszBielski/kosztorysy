<?php

namespace App\Entity;

use App\Service\Functions;
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

    /**
     * @ORM\Column(type="integer")
     */
    private $number;//czy potrzebne?

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Catalog", inversedBy="myChapters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $myCatalog;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

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
    public function readFrom($line)
    {
        $line = Functions::ReplaceCharsAccordingUtf8($line);
        $fields = explode('$',$line );
        $start = strpos($fields[1],'(') + 1;
        $stop = strpos($fields[1],')');
        $this->name = trim(substr($fields[1],$start,$stop-$start));
        $start = strpos($fields[1],'*') + 1;
        $this->description = trim(substr($fields[1],$start));
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
}
