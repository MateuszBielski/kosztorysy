<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\CannotWriteFileException;

// use PHPUnit\Framework\MockObject\Stub\Exception;

//use function foo\func;

class Functions
{
    public static function Hello(){
        return "Hello";
    }
    public function LoopOverMBlistEncodings(Type $var = null)
    {
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
    
    public static function ReplaceCharsAccordingUtf8First(string $str_bytes)
    {
        $str_result = "";
        $rep = null;
        $str_numbers = unpack('C*',$str_bytes);
        foreach ($str_numbers as $n_char) 
        {
            switch ($n_char){
                case 158://ś
                $rep = pack('C2',197,155);
                break;
                case 141://ć
                $rep = pack('C2',196,135);
                break;
                case 146://ł
                $rep = pack('C2',197,130);
                break;
                case 162://ó
                $rep = pack('C2',195,179);
                break;
                case 145://ę
                $rep = pack('C2',196,153);
                break;
                case 167://ż
                $rep = pack('C2',197,188);
                break;
                case 134://ą
                $rep = pack('C2',196,133);
                break;
                default:
                $rep = pack('C',$n_char);

            }
            $str_result .= $rep;
        }

        return $str_result;
    }
    //wzorowane na:
    // https://kasztelan.me/mazovia-utf-8-php-function/
    public static function ReplaceCharsAccordingUtf8(string $str_bytes)
    {
        $zmiana = array(
        chr(0x8F) => chr(0xC4).chr(0x84), 	
        chr(0x95) => chr(0xC4).chr(0x86),
        chr(0x90) => chr(0xC4).chr(0x98), 	
        chr(0x9c) => chr(0xC5).chr(0x81), 	
        chr(0xa5) => chr(0xC5).chr(0x83), 	
        chr(0xa3) => chr(0xC3).chr(0x93), 	
        chr(0x98) => chr(0xC5).chr(0x9A), 	
        chr(0xa0) => chr(0xC5).chr(0xB9), 	
        chr(0xa1) => chr(0xC5).chr(0xBB), 	
        chr(0x86) => chr(0xC4).chr(0x85), 	
        chr(0x8d) => chr(0xC4).chr(0x87), 	
        chr(0x91) => chr(0xC4).chr(0x99), 	
        chr(0x92) => chr(0xC5).chr(0x82), 	
        chr(0xa4) => chr(0xC5).chr(0x84), 	
        chr(0xa2) => chr(0xC3).chr(0xB3), 	
        chr(0x9e) => chr(0xC5).chr(0x9B), 	
        chr(0xa6) => chr(0xC5).chr(0xBA), 	
        chr(0xa7) => chr(0xC5).chr(0xBC)
        );
        return strtr($str_bytes,$zmiana);
    }

    public static function CopyFileStructure($src,$dst,$translateFunc = null)
    {
        // if(file_exists($destPathDirectory)) throw new CannotWriteFileException('Docelowy folder istnieje');
        //mkdir($destPathDirectory);
        // open the source directory 
        $dir = opendir($src);  
    
        // Make the destination directory if not exist 
        @mkdir($dst);  
    
        if ($translateFunc == null) $rewrite = 'copy';
        else{
            $rewrite = function($s,$d) use($translateFunc){
                $f = fopen($d,'wb');
                $convertedString = $translateFunc(fread($s,filesize($s)));
                fwrite($f,$convertedString);
                fclose($f);
            };
        }
        // Loop through the files in source directory 
        while( $file = readdir($dir) ) {  
    
            if (( $file != '.' ) && ( $file != '..' )) {  
                if ( is_dir($src . '/' . $file) )  
                {  
                    Functions::CopyFileStructure($src . '/' . $file, $dst . '/' . $file);  
                }  
                else {  
                    $rewrite($src . '/' . $file, $dst . '/' . $file);  
                }  
            }  
        }  
        closedir($dir); 
    }
    public static function RemoveDirRecursive($dirToRemove)
    {
        if(!file_exists($dirToRemove) || is_file($dirToRemove)) return;
        $dir = opendir($dirToRemove);
        while($file = readdir($dir)){
            $fileDeeper = $dirToRemove.'/'.$file;
            if(($file != '.') && ($file != '..')) {
                if(is_dir($fileDeeper)){
                    Functions::RemoveDirRecursive($fileDeeper);
                }
                else{
                    unlink($fileDeeper);
                }
            }
        }
        closedir($dir);
        rmdir($dirToRemove);
    }
    
}