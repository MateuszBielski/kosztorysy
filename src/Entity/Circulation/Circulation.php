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
        // if (gettype($ind) != 'integer'){
        //     echo "\n".ord($ind);
        //     throw new \Exception('bad int value');
        // } 
        $this->readNameIndex = $ind;
    }
}
