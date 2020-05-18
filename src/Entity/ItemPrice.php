<?php

namespace App\Entity;

use App\Entity\Circulation\Circulation;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ItemPriceRepository")
 */
class ItemPrice extends Circulation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priceValue;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriceValue(): ?int
    {
        return $this->priceValue;
    }

    public function setPriceValue(?int $priceValue): self
    {
        $this->priceValue = $priceValue;

        return $this;
    }
}
