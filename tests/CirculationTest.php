<?php

namespace App\Tests;

use App\Entity\Circulation\Labor_N_U;
use App\Entity\Circulation\Material_N_U;
use App\Service\BuildUniqueCirculations;
use App\Service\CirFunctions;
use PHPUnit\Framework\TestCase;
use App\Entity\Catalog;

require_once('src/Service/Constants.php');

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
       $bazFile = @fopen('resources/Norma3/Kat/4-04/4-04.BAZ','r');
       $circulations = CirFunctions::ReadCirculationsFromBazFile($bazFile);
       fclose($bazFile);
       $material = $circulations['M'][8];
       $this->assertEquals('gwoździe budowlane okrągłe gołe',$material->getName());
       $this->assertEquals(8,$material->getId());
    }
    public function testReadCirculationsFromBazFile_2()
    {
       $bazFile = @fopen('tests/BazFiles/f1.BAZ','r');
       $circulations = CirFunctions::ReadCirculationsFromBazFile($bazFile);
       fclose($bazFile);
       $material = $circulations['M'][6];
       $this->assertEquals('folia polietylenowa szeroka (6 lub 12m) 0.2 mm',$material->getName());
       $this->assertEquals(6,$material->getId());
    }
    public function testBuildArrayOfUniqueCirculations_notEmpty()
    {
        $fileSign = array('4-04','2-02','2W02');
        $bazFile = array();
        $uc = new BuildUniqueCirculations;
        foreach ($fileSign as $sign) {
            $bazFile = @fopen('resources/Norma3/Kat/'.$sign.'/'.$sign.'.BAZ','r');
            $originalCirculations = CirFunctions::ReadCirculationsFromBazFile($bazFile);
            fclose($bazFile);
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
    public function testCorrectContentOfUniqeCirculations()
    {
        // 5 z 3 -> 10
        //
        $uc = new BuildUniqueCirculations;
        $originalCirculations = array ();
        for ($i = 1 ; $i < 4 ; $i++) {
            $bazFile = @fopen('tests/BazFiles/f'.$i.'.BAZ','r');
            $originalCirculations[$i] = CirFunctions::ReadCirculationsFromBazFile($bazFile);
            fclose($bazFile);
            $uc->AddOriginalAndChangeIds($originalCirculations[$i]);
        }
        //czy id prawidłowo zamienione
        $trackedMaterial = $originalCirculations[3]['M'][5];
        $this->assertEquals(10,$trackedMaterial->getId());
        $replacedId = $trackedMaterial->getId();
        $uniqueCirculations = $uc->GetUniqueCirculations();
        $result = '';
        foreach($uniqueCirculations as $item){
            $result .= $item->getName()." ";
        }
        $fexpected = 'tests/BazFiles/sprawdzenieKolejnosci.txt';
        $expected = fread(fopen($fexpected,'r'),filesize($fexpected));
        $this->assertEquals($expected,$result);
    }
    public function testCorrectContentOfUniqeCirculations_2()
    {
        // 5 z 3 -> 10
        //
        $uc = new BuildUniqueCirculations;
        $originalCirculations = array ();
        for ($i = 4 ; $i < 6 ; $i++) {
            $bazFile = @fopen('tests/BazFiles/f'.$i.'.BAZ','r');

            $originalCirculations[$i] = CirFunctions::ReadCirculationsFromBazFile($bazFile);
            fclose($bazFile);
            $uc->AddOriginalAndChangeIds($originalCirculations[$i]);
        }
        $trackedMaterial = $originalCirculations[4]['M'][2];
        // $this->assertEquals(10,$trackedMaterial->getId());
        $replacedId = $trackedMaterial->getId();
        $uniqueCirculations = $uc->GetUniqueCirculations();
        $result = '';
        foreach($uniqueCirculations as $item){
            $result .= $item->getName()." ";
        }
        $this->assertEquals('gwoździe drewno cegła ',$result);
    }
    public function testAddCirculationsFromCatalogCollection()
    {
        $catFileNames = array('resources/Norma3/Kat/KNZ-14/',
                            'resources/Norma3/Kat/S-215/',
                        'resources/Norma3/Kat/0-10/');
        $catalogs = array();
        for($i = 0 ; $i < 3 ; $i++)
        {
            $catalog = new Catalog;
            $catalog->ReadFromDir($catFileNames[$i],BAZ_FILE_DIST);
            $catalogs[] = $catalog;
        }
        $uc = new BuildUniqueCirculations;
        $uc->AddCirculationsFromCatalogCollection($catalogs);
        $this->assertEquals(272,count($uc->GetUniqueCirculations()));//272 nie było dokładnie liczone
    }

}
