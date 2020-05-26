<?php

namespace App\Tests;

use App\Entity\Circulation\Equipment_N_U;
use App\Entity\Circulation\Labor_N_U;
use App\Entity\Circulation\Material_N_U;
use App\Entity\PriceList;
use PHPUnit\Framework\TestCase;

class PriceListTest extends TestCase
{
    public function testCreateRandomPrices()
    {
        $minPrice = 0.95;
        $maxPrice = 301.34;
        $circulationNUs = array();
        $circulationNUs[] = new Labor_N_U;
        $circulationNUs[] = new Labor_N_U;
        $circulationNUs[] = new Material_N_U;
        $circulationNUs[] = new Material_N_U;
        $circulationNUs[] = new Material_N_U;
        $circulationNUs[] = new Equipment_N_U;
        $circulationNUs[] = new Equipment_N_U;
        $priceList = new PriceList;
        $priceList->CreateRandomPrices($circulationNUs,$minPrice,$maxPrice);
        $this->assertEquals(7,count($priceList->getPrices()));
        foreach($priceList->getPrices() as $ip)
        {
            $price = $ip->getPriceValue()/100;
            $this->assertTrue($price >= $minPrice && $price <= $maxPrice);
        }
    }
}
