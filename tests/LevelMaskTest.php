<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
require_once('src/Service/Constants.php');


class LevelMaskTest extends TestCase
{
    public function testDefineExampleNumberConstant()
    {
        // echo __DIR__;
        define('FIVE',5);
        $this->assertEquals(5,FIVE);
        $this->assertEquals(6,SIX);
    }
    public function testDefineCatalogConst()
    {
        // echo decbin(0b10000000 & CATALOG );
        $this->assertTrue(0b10000000 & CATALOG ? true:false);
        $this->assertFalse(0b01000000 & CATALOG ? true:false);
    }
    public function testDefineChapterConst()
    {
        // echo decbin(0b10000000 & CATALOG );
        $this->assertTrue(0b01000000 & CHAPTER ? true:false);
        $this->assertFalse(0b00100000 & CHAPTER ? true:false);
    }
    public function testDefineTableConst()
    {
        // echo decbin(0b10000000 & CATALOG );
        $this->assertTrue(0b00100000 & TABLE ? true:false);
        $this->assertFalse(0b00010000 & TABLE ? true:false);
    }
    public function testDefineTableRowConst()
    {
        // echo decbin(0b10000000 & CATALOG );
        $this->assertTrue(0b00010000 & TABLE_ROW ? true:false);
        $this->assertFalse(0b00001000 & TABLE_ROW ? true:false);
    }
}
