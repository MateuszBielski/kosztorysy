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
     * @ORM\Column(type="string", length=60)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ItemPrice", mappedBy="priceList",orphanRemoval=true)
     */
    private $itemPrices;

    public function __construct()
    {
        // $this->prices = new ArrayCollection();
        $this->itemPrices = new ArrayCollection();
    }

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
    
    /**
     * @return Collection|ItemPrice[]
     */
    public function getItemPrices(): Collection
    {
        return $this->itemPrices;
    }

    public function addItemPrice(ItemPrice $itemPrice): self
    {
        if (!$this->itemPrices->contains($itemPrice)) {
            $this->itemPrices[] = $itemPrice;
            $itemPrice->setPriceList($this);
        }

        return $this;
    }

    public function removeItemPrice(ItemPrice $itemPrice): self
    {
        if ($this->itemPrices->contains($itemPrice)) {
            $this->itemPrices->removeElement($itemPrice);
            // set the owning side to null (unless already changed)
            if ($itemPrice->getPriceList() === $this) {
                $itemPrice->setPriceList(null);
            }
        }

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
        return -1;
    }

    public function AssignRandomPrices(array $prices,$min,$max)
    {
        foreach($prices as $pr)
        $pr->setPriceValue(rand($min * 100, $max * 100));
    }
}
