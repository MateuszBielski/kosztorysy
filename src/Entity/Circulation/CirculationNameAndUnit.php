<?php

namespace App\Entity\Circulation;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CirculationNameAndUnitRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminator")
 */
abstract class CirculationNameAndUnit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $unit;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $eto;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
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

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getEto(): ?string
    {
        return $this->eto;
    }

    public function setEto(?string $eto): self
    {
        $this->eto = $eto;

        return $this;
    }
    public function setParametersFromBAZline(string $line)
    {
        $line = trim($line);
        $this->unit = trim(substr($line,-3));
        $len = strlen($line) - 3;
        $params = explode('$',substr($line,0,$len));
        $this->eto = $params[0];
        $this->name = $params[1];
    }
}
