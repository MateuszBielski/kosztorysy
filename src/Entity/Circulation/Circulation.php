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
    protected $id;

    /**
     * @ORM\Column(type="float")
     */
    protected $value = 0.0;
    protected $readNameIndex;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Circulation\CirculationNameAndUnit",cascade={"persist"},fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $nameAndUnit;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $groupNumber = 0;

    private $price = 0;
    private $koszt = 0.0;
    private $jednostkaDlaNakladuJednostkowego = '';
    
    
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
        if($this->nameAndUnit == null) return '';
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

    public function setPrice(int $val)
    {
        $this->price = $val;
    }

    public function getPriceDivBy100(): float
    {
        return $this->price/100;
    }
    public function getKoszt()
    {
        return $this->koszt;
    }
    public function setKoszt(float $koszt)      
    {
        $this->koszt = $koszt;
    }

    public function obliczKosztDlaObmiaru(float $obmiar)
    {
        $this->koszt = $obmiar * $this->value * $this->price / 100;
    }

    public function getJednostkaDlaCenyJEdnostkowej()
    {
        $u = $this->nameAndUnit->getUnit();
        $res = 'zł';
        if($u != '%') $res .="/$u";
        return $res;
    }
    public function UstalJednostkiDlaJednostkiObmiaru(?string $jednObm)
    {
        $u = $this->nameAndUnit->getUnit();
        
        if($u != '%') $u .= '/'.$jednObm;
        $this->jednostkaDlaNakladuJednostkowego = $u;
    }
    public function getJednostkaDlaNakladuJednostkowego()
    {
        return $this->jednostkaDlaNakladuJednostkowego;
    }
    
}
