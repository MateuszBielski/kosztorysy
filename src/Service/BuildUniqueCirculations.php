<?php

namespace App\Service;

class BuildUniqueCirculations
{
    private $namesTocompare = array();

    private $uniqueCirculations = array();
    private $index = 1;

    public function AddOriginalAndChangeIds($circulations)
    {
        $CircCat = array('R','M','S');
        foreach ($CircCat as $cat) {
            if (!array_key_exists($cat,$circulations)) continue;
            foreach ($circulations[$cat] as $cir) {
                $name = $cir->getName();
                
                $foundIndex = array_search($name,$this->namesTocompare);
                if ($foundIndex ){
                    $cir->setId($foundIndex);
                }else {
                    $this->namesTocompare[$this->index] = $name; 
                    $this->uniqueCirculations[$this->index] = $cir;
                    $cir->setId($this->index);
                    $this->index++;
                }
            }
        }
    }

    public function GetUniqueCirculations()
    {
        return $this->uniqueCirculations;
    }
}