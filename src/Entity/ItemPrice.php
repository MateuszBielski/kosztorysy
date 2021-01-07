<?php

namespace App\Entity;

use App\Entity\Circulation\CirculationNameAndUnit;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ItemPriceRepository")
 */
class ItemPrice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PriceList", inversedBy="itemPrices")
     */
    private $priceList;

    /**
     * @ORM\Column(type="integer")
     */
    private $priceValue;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Circulation\CirculationNameAndUnit")
     * @ORM\JoinColumn(nullable=false)
     */
    private $name_and_unit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriceList(): ?PriceList
    {
        return $this->priceList;
    }

    public function setPriceList(?PriceList $priceList): self
    {
        $this->priceList = $priceList;

        return $this;
    }

    public function getPriceValue(): ?int
    {
        return $this->priceValue;
    }

    public function setPriceValue(int $priceValue): self
    {
        $this->priceValue = $priceValue;

        return $this;
    }

    public function getNameAndUnit(): ?CirculationNameAndUnit
    {
        return $this->name_and_unit;
    }

    public function setNameAndUnit(?CirculationNameAndUnit $name_and_unit): self
    {
        $this->name_and_unit = $name_and_unit;

        return $this;
    }
}
