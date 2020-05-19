<?php

namespace App\Tests;

use App\Entity\Circulation\Material;
use App\Entity\ItemPrice;
use PHPUnit\Framework\TestCase;

class ItemPriceTest extends TestCase
{
    public function testFactoryItemPricesFromCirculations()
    {
        $materials = array(new Material,new Material, new Material);
        $itemPrices = ItemPrice::FactoryFromCirculations($materials);
        $this->assertEquals(3,count($itemPrices));
    }
}
