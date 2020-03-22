<?php

namespace App\Service;



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
        chr(0x8F) => chr(0xC4).chr(0x84),//Ą 	
        chr(0x95) => chr(0xC4).chr(0x86),//Ć
        chr(0x90) => chr(0xC4).chr(0x98),//Ę 	
        chr(0x9c) => chr(0xC5).chr(0x81),//Ł
        chr(0xa5) => chr(0xC5).chr(0x83),//Ń	
        chr(0xa3) => chr(0xC3).chr(0x93),//Ó 	
        chr(0x98) => chr(0xC5).chr(0x9A),//Ś
        chr(0xa0) => chr(0xC5).chr(0xB9),//Ż
        chr(0xa1) => chr(0xC5).chr(0xBB),//Ź
        chr(0x86) => chr(0xC4).chr(0x85),//ą 	
        chr(0x8d) => chr(0xC4).chr(0x87),//ć
        chr(0x91) => chr(0xC4).chr(0x99),//ę
        chr(0x92) => chr(0xC5).chr(0x82),//ł	
        chr(0xa4) => chr(0xC5).chr(0x84),//ń	
        chr(0xa2) => chr(0xC3).chr(0xB3),//ó	
        chr(0x9e) => chr(0xC5).chr(0x9B),//ś	
        chr(0xa6) => chr(0xC5).chr(0xBA),//ż	
        chr(0xa7) => chr(0xC5).chr(0xBC),//ź
        // chr(0x20) => chr(0x2b) //spacja na +
        );
        return strtr($str_bytes,$zmiana);
    }
    public static function CopyOneFileWithTranslate($srcFile,$destPath,$translateFunc){
        @mkdir($destPath);
        $d = $destPath.basename($srcFile);
        $newFile = fopen($d,'wb');
        $sourceHandle = fopen($srcFile,'rb');
        $sourceLen = filesize($srcFile);
        $convertedString = $translateFunc(fread($sourceHandle,$sourceLen));
        fclose($sourceHandle);
        fwrite($newFile,$convertedString);
        fclose($newFile);
    }

    public static function CopyFileStructure($src,$dst,$translateFunc = null)
    {
        $dir = opendir($src);  
    
        // Make the destination directory if not exist 
        @mkdir($dst);
        
        
        // Loop through the files in source directory 
        while( $file = readdir($dir) ) {  
    
            if (( $file != '.' ) && ( $file != '..' )) {  
                
                $sourceFileName = $src . '/' . $file;
                if ( is_dir($src . '/' . $file) )  
                {  
                    Functions::CopyFileStructure($sourceFileName, $dst . '/' . $file,$translateFunc);  
                }  
                else {

                    $sourceLen = filesize($sourceFileName);
                    if ($translateFunc != null && $sourceLen)
                    {
                        $rewrite = function($s,$d) use($translateFunc,$sourceLen){
                            $newFile = fopen($d,'wb');
                            $sourceHandle = fopen($s,'rb');
                            $convertedString = $translateFunc(fread($sourceHandle,$sourceLen));
                            fclose($sourceHandle);
                            fwrite($newFile,$convertedString);
                            fclose($newFile);
                        };
                    }
                    else $rewrite = 'copy';

                    $rewrite($sourceFileName, $dst . '/' . $file);  
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
    public static function FindFileByDirNameAndOpen($dirPath,$fileExtension)
    {
        // $chapterFile = @fopen($dirName.'/'.$catBaseName.'.D^D','r');
        if (!is_dir($dirPath)) return false;
        Functions::SlashTrim($dirPath);
        $upperDirBaseName = strtoupper(basename($dirPath));
        $fileNameUpperSearched = $upperDirBaseName.'.'.$fileExtension;
        // echo $fileNameUpperSearched;
        $dir = opendir($dirPath);
        while($file = readdir($dir)){
            if($file == '.' || $file == '..')continue;
            if(strtoupper($file) == $fileNameUpperSearched) {
                $file= $dirPath.'/'.$file;
                return fopen($file,'r');
            }
            // if(!is_file($file) ) continue;

        }
        return false;
    }
    public static function SlashTrim(&$dirPath)
    {
        if (substr($dirPath,-1,1) == '/') $dirPath = rtrim($dirPath,"/");
    }
    public static function AppendixForDuplicateKeys($key,array $arr)
    {
        if (array_key_exists($key,$arr)) $key .='x';
        return $key;
    }
}