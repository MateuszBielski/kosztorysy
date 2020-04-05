<?php

namespace App\Tests;

use App\Service\Functions;
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    public function testSomething()
    {
        $str = '59$1$$$^$$ - 05 $szt.$2$3$2$4$1$8$80$1$15$23$';
        $result = Functions::FindSlicePosition($str,'$',7);
        $this->assertEquals(17,$result);
    }
}
