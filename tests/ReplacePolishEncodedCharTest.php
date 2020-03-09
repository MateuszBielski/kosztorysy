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
    
    private $path_file1 = __DIR__.'/../resources/użyte_funkcje.txt';
    private $path_file2 = __DIR__.'/../resources/Norma3/Kat/0-22/0-22R5.OP';
    private $path_file3 = __DIR__.'/../resources/litery.txt';
    private $path_file4 = __DIR__.'/../resources/polskieZnaki.txt';
    private $path_file5 = __DIR__.'/../resources/kodowanieUTF8.txt';
    public function testSomething()
    {
        $this->assertTrue(true);
    }
    public function testFunctionHello()         
    {
        $this->assertEquals("Hello",Functions::Hello());
        
    }
    public function testFileExist()
    {
        $this->assertTrue(file_exists($this->path_file1));
    }
    public function testOpenFile()
    {
    
        $f = fopen($this->path_file3,'rb');
        $raw_data = fread($f,filesize($this->path_file3));//
        // $u_data = unpack('C*',$raw_data);
        // $str_data = implode(" ",$u_data);
        $this->assertEquals('abcdef 12345',$raw_data);
        fclose($f);
    }
    public function testReplaceCharsAccordingUtf8()
    {
        // $f = fopen($this->path_file4,'rb');
        // $byteCharStreamToReplace = fread($f,filesize($this->path_file4));
        // Functions::CharsAccordingUtf8()
        // print_r(mb_list_encodings());
        $str_bytes = pack('C*',158,141,146,162,145,167,134);
        $replaced_chars = Functions::ReplaceCharsAccordingUtf8($str_bytes);
        $u_data = unpack('C*',$replaced_chars);
        $str_numbers = implode(" ",$u_data);
        $this->assertEquals('197 155 196 135 197 130 195 179 196 153 197 188 196 133',$str_numbers);

    }

    public function testUnpackigReadStream()
    {
        $f = fopen($this->path_file5,'rb');
        $raw_data = fread($f,filesize($this->path_file5));
        $u_data = unpack('C*',$raw_data);
        $str_data = implode(" ",$u_data);
        $this->assertEquals('97 98 99 100 101 102 10 197 155 196 135 197 130 195 179 196 153 197 188 196 133',$str_data);
        fclose($f);
    }

    public function testReadSiglePolishCharPacked()
    {
        $polichChar = pack('C2',197,155);
        $this->assertEquals('ś',$polichChar);
    }
    public function testReadAndConvertNormaO(Type $var = null)
    {
        $f = fopen($this->path_file2,'rb');
        $raw_data = fread($f,filesize($this->path_file2));
        // $u_data = unpack('C*',$raw_data);
        $convertedString = Functions::ReplaceCharsAccordingUtf8($raw_data);
        echo $convertedString;

    }
}
