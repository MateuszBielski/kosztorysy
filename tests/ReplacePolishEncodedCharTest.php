<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use App\Service\Functions;
// use function App\Service\Functions\Hello;

// function Hello(){
//     return "Hello";
// }

class ReplacePolishEncodedCharTest extends TestCase
{
    public function testSomething()
    {
        $this->assertTrue(true);
    }
    public function testFunctionHello()         
    {
        $this->assertEquals("Hello",Functions::Hello());
        
    }
}
