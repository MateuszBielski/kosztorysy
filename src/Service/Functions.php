<?php

namespace App\Service;

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
    
    public static function ReplaceCharsAccordingUtf8(string $str_bytes)
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
    //inna wersja
    // https://kasztelan.me/mazovia-utf-8-php-function/
    
}