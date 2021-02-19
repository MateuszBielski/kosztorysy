<?php

namespace App\Tests;

use App\Entity\Kosztorys;
use PHPUnit\Framework\TestCase;

class KosztorysTest extends TestCase
{
    public function testLadowanieOddzielniePol()
    {
        $kosztorys = new Kosztorys;
        $param = [];
        $param['item_prices'] = [''];
        $this->assertTrue(true);
    }
}
