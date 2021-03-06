<?php

namespace App\Tests;

use App\Entity\Circulation\Labor_N_U;
use App\Entity\Circulation\Material_N_U;
use App\Service\BuildUniqueCirculations;
use App\Service\CirFunctions;
use PHPUnit\Framework\TestCase;
use App\Entity\Catalog;
use App\Entity\Circulation\Equipment_N_U;
use App\Entity\Circulation\Labor;
use App\Entity\Circulation\Material;
use App\Entity\Circulation\Equipment;

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
    public function testAddOriginalAndChangeIds()
    {
        $rob1 = new Labor_N_U;
        $rob2 = new Labor_N_U;
        $rob3 = new Labor_N_U;
        $rob1->setName('robotnicy');
        $rob1->setUnit('r-g');

        $rob2->setName('robotnicy');
        $rob2->setUnit('r-d');
        $rob3->setName('robotnicy');
        $rob3->setUnit('r-g');
        $cir1 = array();
        $cir2 = array();
        
        $cir1['R'][] = $rob1;
        $cir1['R'][] = $rob2;
        $cir2['R'][] = $rob3;
        
        $uc = new BuildUniqueCirculations;
        $uc->AddOriginalAndChangeIds($cir1);
        $uc->AddOriginalAndChangeIds($cir2);

        $this->assertEquals(2,count($uc->GetUniqueCirculations()));
    }
    public function testGenerateQueryToInsert()
    {
        $tLine = '3950000 000 060$drewno na stemple (okragłe) iglaste korowane śr. 6 do 20 cm m3';
        $material = new Material_N_U;
        $material->setParametersFromBAZline($tLine);
        $material->setId(12);
        $expectedQuery = "(12,'drewno na stemple (okragłe) iglaste korowane śr. 6 do 20 cm','m3','3950000 000 060','material_n_u'),";
        $this->assertEquals($expectedQuery,$material->GenerateQueryToInsert());
    }
    public function testAddSelfToCorrectSubArray()
    {
        $circulationsNUseparated = array();
        $circulations = array(
            new Labor_N_U,
            new Labor_N_U,
            new Material_N_U,
            new Material_N_U,
            new Equipment_N_U,
            new Material_N_U,
            new Equipment_N_U
        );
        foreach($circulations as $cir)$cir->AddSelfToCorrectSubArray($circulationsNUseparated);
        $this->assertEquals('232',count($circulationsNUseparated['R']).count($circulationsNUseparated['M']).count($circulationsNUseparated['S']));
    }
    public function testObliczKosztDlaObmiaru()
    {
        $rob = new Labor;
        $rob->setValue(23.4);
        $rob->setPrice(252);
        $rob->obliczKosztDlaObmiaru(12.1);
        $this->assertEquals(713.51,$rob->getKoszt());//koszt jest zaokrąglony
        
    }
    public function testJednostkaDlaCenyJednostkowej()
    {
        $material = new Material;
        $nau = new Material_N_U;
        $nau->setUnit('m2');
        $material->setNameAndUnit($nau);
        $this->assertEquals('zł/m2',$material->getJednostkaDlaCenyJEdnostkowej());
    }
    public function testJednostkaDlaCenyJednostkowe_procenty()
    {
        $material = new Material;
        $nau = new Material_N_U;
        $nau->setUnit('%');
        $material->setNameAndUnit($nau);
        $this->assertEquals('zł',$material->getJednostkaDlaCenyJEdnostkowej());
    }
    public function testJednostkaDlaNakladuJednostkowego()
    {
        
        $sprz = new Equipment;
        $nau = new Equipment_N_U;
        $nau->setUnit('m-g');
        $sprz->setNameAndUnit($nau);
        $sprz->UstalJednostkiDlaJednostkiObmiaru('m3');
        $this->assertEquals('m-g/m3',$sprz->getJednostkaDlaNakladuJednostkowego());
    }
    public function testJednostkaDlaNakladuJednostkowego_procenty()
    {
        
        $sprz = new Equipment;
        $nau = new Equipment_N_U;
        $nau->setUnit('%');
        $sprz->setNameAndUnit($nau);
        $sprz->UstalJednostkiDlaJednostkiObmiaru('m3');
        $this->assertEquals('%',$sprz->getJednostkaDlaNakladuJednostkowego());
    }

    public function testObliczKosztJednostkowy()
    {
        $mat = new Material;
        // $nau = new Material_N_U;
        // $nau->set
        $mat->setValue(1);
        $mat->setPrice(200);
        // $mat->obliczKosztDlaObmiaru(3);
        $mat->ObliczKosztJednostkowy();
        $this->assertEquals(2.0,$mat->getKosztJednostkowy());

    }
    public function testObliczNakladDlaObmiaru()
    {
        $mat = new Material;
        
        $mat->setValue(1);
        $mat->ObliczNakladDlaObmiaru(23);
        $this->assertEquals(23,$mat->getNaklad());
    }
}
