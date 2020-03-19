<?php

namespace App\Service;

class BuildUniqueCirculations
{
    private $namesTocompare = array();

    private $uniqueCirculations = array();

    public function AddOriginalAndChangeIds($circulations)
    {
        $index = 1;
        $CircCat = array('R','M','S');
        foreach ($CircCat as $cat) {
            if (!array_key_exists($cat,$circulations)) continue;
            foreach ($circulations[$cat] as $cir) {
                $name = $cir->getName();
                $foundIndex = array_search($name,$this->namesTocompare);
                if ($foundIndex){
                    $cir->setId($foundIndex);
                }else {
                    $this->namesTocompare[$index] = $name; 
                    $this->uniqueCirculations[$index] = $cir;
                    $cir->setId($index);
                    $index++;
                }
            }
        }
    }

    public function GetUniqueCirculations()
    {
        return $this->uniqueCirculations;
    }
}