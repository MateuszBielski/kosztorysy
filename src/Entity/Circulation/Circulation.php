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
    protected $value;
    protected $readNameIndex;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Circulation\CirculationNameAndUnit",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $nameAndUnit;

    
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
        if($ind > 999)$ind = $ind%1000;
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

    public function getNameAndUnit(): ?CirculationNameAndUnit
    {
        return $this->nameAndUnit;
    }

    public function setNameAndUnit(?CirculationNameAndUnit $nameAndUnit): self
    {
        $this->nameAndUnit = $nameAndUnit;

        return $this;
    }
}
