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
    public function testRemoveLongSpaces()
    {
        $textWithSpaces = 'Rurociągi z rur propylenowych na ścianach w budynkach                         mieszkalnych';
        $this->assertEquals('Rurociągi z rur propylenowych na ścianach w budynkach mieszkalnych',Functions::RemoveLongSpaces($textWithSpaces));
    }

    public function testRemoveIllegalUtf8Characters()
    {
        $rawUTF8 = 'łopat$a c_ięgno '.chr(0xd1).'żarów'.chr(0xc3).'ka';
        $converted = iconv("UTF-8","UTF-8//IGNORE",$rawUTF8);
        $this->assertEquals('łopat$a c_ięgno żarówka',$converted);
    }
}
