<?php

namespace App\Service;

use App\Entity\Circulation\Equipment_N_U;
use App\Entity\Circulation\Labor_N_U;
use App\Entity\Circulation\Material_N_U;

class CirFunctions
{

    public static function ReadCirculationsFromBazFile($bazFile): array
    {
        // $circulations = new ArrayCollection();
        $circulations = array();
        // $bazFile = @fopen($bazFileName,'r');
        $n_R = intval(fgets($bazFile));
        $n_M = intval(fgets($bazFile));
        $n_S = intval(fgets($bazFile));
        $uniqueId = 1;
        $read = function($n_,$className,$CircCat) use (&$bazFile,&$circulations,&$uniqueId){
            for ($i = 1; $i <= $n_; $i++) {
                $circulation = new $className;
                $circulation->setParametersFromBAZline(Functions::ReplaceCharsAccordingUtf8(fgets($bazFile)));
                $circulation->setId($uniqueId++);
                $circulations[$CircCat][$i] = $circulation;
            }

        };
        $read($n_R,Labor_N_U::class,'R');
        $read($n_M,Material_N_U::class,'M');
        $read($n_S,Equipment_N_U::class,'S');
        return $circulations;
    }
    
}
