<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class BuildUniqueCirculations
{
    private $namesTocompare = array();

    private $uniqueCirculations = array();
    private $index = 1;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager = null)
    {
        $this->entityManager = $entityManager;
    }

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

    public function setUniqueCirculations($uc)
    {
        $this->uniqueCirculations = $uc;
    }
    public function AddCirculationsFromCatalogCollection(array $catalogs)
    {
        foreach($catalogs as $cat)
        {
            $this->AddOriginalAndChangeIds($cat->getMyCirculationsNU());
        }
    }
    public function PersistUniqueCirculations()
    {
        foreach($this->uniqueCirculations as $cir)
        {
            $this->entityManager->persist($cir);
        }
        $this->entityManager->flush();
    }
}