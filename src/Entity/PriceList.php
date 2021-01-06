<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PriceListRepository")
 */
class PriceList
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ItemPrice", mappedBy="priceList")
     */
    //, casscade={"persist"}
    private $prices;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $name;

    public function __construct()
    {
        $this->prices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|ItemPrice[]
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(ItemPrice $price): self
    {
        if (!$this->prices->contains($price)) {
            $this->prices[] = $price;
            $price->setPriceList($this);
        }

        return $this;
    }

    public function removePrice(ItemPrice $price): self
    {
        if ($this->prices->contains($price)) {
            $this->prices->removeElement($price);
            // set the owning side to null (unless already changed)
            if ($price->getPriceList() === $this) {
                $price->setPriceList(null);
            }
        }

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
    public function CreateRandomPrices(array $circulations_nu, $min, $max)
    {
        $prices = [];
        foreach ($circulations_nu as $cnu) {
            $itemPrice = new ItemPrice;
            $itemPrice->setPriceValue(rand($min * 100, $max * 100));
            $itemPrice->setNameAndUnit($cnu);
            // $this->prices[] =  $itemPrice;
            $prices[] = $itemPrice;
            $itemPrice->setPriceList($this);
        }
        return $prices;
    }
    public function getAmonutOfPrices()
    {
        return count($this->prices);
    }
}
