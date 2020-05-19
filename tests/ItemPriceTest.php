<?php

namespace App\Tests;

use App\Entity\Circulation\Material;
use App\Entity\Circulation\Material_N_U;
use App\Entity\ItemPrice;
use PHPUnit\Framework\TestCase;

class ItemPriceTest extends TestCase
{

    public function testInitializeItemPrice()
    {
        $mat = new Material;
        $mat_n_u = new Material_N_U;
        $mat_n_u->setName('materiał');
        $mat_n_u->setEto('0023');
        $mat_n_u->setUnit('m4');
        $mat->setValue(35.2);
        $mat->setnameAndUnit($mat_n_u);
        $ip = new ItemPrice;
        $ip->Initialize($mat);
        $this->assertEquals($mat_n_u,$ip->getNameAndUnit());

        $this->assertEquals(35.2,$ip->getValue());
        $this->assertEquals(0,$ip->getGroupNumber());
    }
    public function testFactoryItemPricesFromCirculations()
    {
        $materials = array(new Material, new Material, new Material);
        $itemPrices = ItemPrice::FactoryFromCirculations($materials);
        $this->assertEquals(3, count($itemPrices));
    }
}
