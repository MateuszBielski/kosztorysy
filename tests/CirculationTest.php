<?php

namespace App\Tests;

use App\Entity\Circulation\Labor_N_U;
use App\Entity\Circulation\Material_N_U;
use App\Service\BuildUniqueCirculations;
use App\Service\CirFunctions;
use PHPUnit\Framework\TestCase;

class CirculationTest extends TestCase
{
       
    public function testReadCirculationFieldsFromTextLine()
    {
        $tLine = '3950000 000 060$drewno na stemple (okragłe) iglaste korowane śr. 6 do 20 cm m3';
        $material = new Material_N_U;
        $material->setParametersFromBAZline($tLine);
        $this->assertEquals('m3',$material->getUnit());
        $this->assertEquals('drewno na stemple (okragłe) iglaste korowane śr. 6 do 20 cm',$material->getName());
    }
    //testReadCirculationsFromCatalogPath() -czy warto?
    public function testReadCirculationsFromBazFile()
    {
       $bazFile = 'resources/Norma3/Kat/4-04/4-04.BAZ';
       $circulations = CirFunctions::ReadCirculationsFromBazFile($bazFile);
       $material = $circulations['M'][8];
       $this->assertEquals('gwoździe budowlane okrągłe gołe',$material->getName());
       $this->assertEquals(8,$material->getId());
    }
    public function testBuildArrayOfUniqueCirculations_notEmpty()
    {
        $fileSign = array('4-04','2-02','2W02');
        $bazFile = array();
        $uc = new BuildUniqueCirculations;
        foreach ($fileSign as $sign) {
            $bazFile = 'resources/Norma3/Kat/'.$sign.'/'.$sign.'.BAZ';
            $originalCirculations = CirFunctions::ReadCirculationsFromBazFile($bazFile);
            $uc->AddOriginalAndChangeIds($originalCirculations);
        }
        $uniqueCirculations = $uc->GetUniqueCirculations();
        $this->assertGreaterThan(0,count($uniqueCirculations));
        $result = '';
        for($i = 1 ; $i < 7 ; $i++) {
            $result .= $uniqueCirculations[$i]->getId();
        }
        $this->assertEquals('123456',$result);
        //check state added to unique or not
    }
    public function _testCorrectContentOfUniqeCirculations()
    {
        
    }

}
