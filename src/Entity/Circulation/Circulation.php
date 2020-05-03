<?php

namespace App\Entity\Circulation;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CirculationRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminator")
 */
class Circulation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    protected $value = 0.0;
    protected $readNameIndex;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Circulation\CirculationNameAndUnit",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $nameAndUnit;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $groupNumber = 0;

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }
    public function getReadNameIndex()
    {
        
        return $this->readNameIndex;
    }
    public function setReadNameIndex($ind)
    {
        $ind = intval($ind);
        if($ind > 999){
            $indRest = $ind%1000;
            $this->groupNumber = ($ind-$indRest)/1000;
            $ind = $indRest;
        }
        // if (gettype($ind) != 'integer'){
        //     echo "\n".ord($ind);
        //     throw new \Exception('bad int value');
        // } 
        $this->readNameIndex = $ind;
    }
    public function getName()
    {
        return $this->nameAndUnit->getName(); 
    }
    public function getUnit()
    {
        return $this->nameAndUnit->getUnit();
    }

    public function getNameAndUnit(): ?CirculationNameAndUnit
    {
        return $this->nameAndUnit;
    }

    public function setNameAndUnit(?CirculationNameAndUnit $nameAndUnit): self
    {
        $this->nameAndUnit = $nameAndUnit;

        return $this;
    }

    public function getGroupNumber(): ?int
    {
        return $this->groupNumber;
    }

    public function setGroupNumber(?int $groupNumber): self
    {
        $this->groupNumber = $groupNumber;

        return $this;
    }
}
