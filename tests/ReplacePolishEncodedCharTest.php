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
    public function testReplacCharsAccordingUtf8()
    {
        // $f = fopen($this->path_file4,'rb');
        // $byteCharStreamToReplace = fread($f,filesize($this->path_file4));
        // Functions::CharsAccordingUtf8()
        // print_r(mb_list_encodings());
        $str_bytes = pack('C*',158,141,146,162,145,167,134);
        setlocale(LC_ALL, 'pl_PL');
        $charset = array_slice(mb_list_encodings(),1);
        foreach ($charset as $key => $cSet) {
            try{
                $converted_str = iconv($cSet,'UTF-8',$str_bytes);
            }catch(Exception $e){
                continue;
            }
            if ($converted_str == 'śćłóężą')
            print_r($key.$cSet);
            break;
        }
        
        var_dump($converted_str);

    }
}
