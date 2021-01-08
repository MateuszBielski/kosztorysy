<?php

namespace App\Entity;

use App\Entity\Circulation\Circulation;
use Doctrine\ORM\Mapping as ORM;

class ItemPriceOld extends Circulation
{

    
    private $priceValue = 0;

    
    private $priceList;

    public function getPriceValue(): ?int
    {
        return $this->priceValue;
    }

    public function setPriceValue(?int $priceValue): self
    {
        $this->priceValue = $priceValue;

        return $this;
    }
    public function Initialize(Circulation $c)
    {
        $this->value = $c->getValue();
        $this->nameAndUnit = $c->getNameAndUnit();
        $this->groupNumber = $c->getGroupNumber();
    }
    public static function FactoryFromCirculations($circulations)
    {
        $itemPrices = array();
        foreach($circulations as $cir)
        {
            $ip = new ItemPrice;
            $ip->Initialize($cir);
            $itemPrices[] = $ip;
        }
        return $itemPrices;
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
}
