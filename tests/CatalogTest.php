<?php

namespace App\Tests;

use App\Entity\Catalog;
use PHPUnit\Framework\TestCase;
require_once('src/Service/Constants.php');

class CatalogTest extends TestCase
{
    
    //nie będzie działał bo system nie daje zawartości w przewidywanej kolejności
    public function _testReadFourthNameFromOpenDir()
    {
        $dir = opendir('resources/Norma3/Kat/');
        for($i = 0 ; $i < 4 ; $i++){

            $catDir = readdir($dir);
            $catName = basename($catDir);
            echo "\n".$catName;
        } 
        $this->assertEquals('0-13',$catName);

        // $result = $catalog->getName();
        // $this->assertEquals('KNR 0-13',$result);
    }

    public function testReadCatalogName()
    {
        $catFile = 'resources/Norma3/Kat/2-03/';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile);
        $this->assertEquals('KNR   2-03',$catalog->getName());
    }
    public function testReadCatalogName_2()
    {
        $catFile = 'resources/Norma3/Kat/2W16/';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile);
        $this->assertEquals('KNR(W) 2-16',$catalog->getName());
    }
    public function testCatalogCountReadChapters()
    {
        $catFile = 'resources/Norma3/Kat/2-02/';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile);
        $chapters = $catalog->getMyChapters();
        // foreach($chapters as $key => $chapt){
        //     echo "\n".$key.' '.$chapt->getName();
        // }
        $this->assertEquals(25,count($chapters));
    }
    public function testLoadCatalogsFromCommonDirWithoutRead()
    {
        $commonDir = 'resources/Norma3/Kat/';
        $catalogs = Catalog::LoadFrom($commonDir);//DESCRIPaRMS|BAZ_FILE_DIST
        $this->assertEquals(258,count($catalogs));
    }
    public function testLoadBazFilesDuringReadCatDir()
    {
        $catFile = 'resources/Norma3/Kat/0-10/';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile,BAZ_FILE_DIST);
        $circ = $catalog->getMyCirculationsNU();
        $this->assertEquals('pianka izolacyjna',$circ['M'][4]->getName());

    }
    public function testAssignNamesAndUnitforCirculation()
    {
        $catFile = 'resources/Norma3/Kat/2W18/';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile,DESCRIPaRMS|BAZ_FILE_DIST);
        // echo "\nXXX".count($catalog->getMyChapters());
        // foreach($catalog->getMyChapters() as $cat){
        //     echo "\n".$cat->getName();
        // }
        $tr = $catalog->getMyChapters()['Rozdział 08']->getTables()[8]->getTableRows()[2];
        $this->assertEquals('rury wodociągowe ciśnieniowe z polietylenu',$tr->getMaterials()[0]->getName());
        $this->assertEquals('samochód dostawczy 0.9 t',$tr->getEquipments()[0]->getName());
    }
    public function testCatalogDescription()
    {
        $d_dLine = 'Z-NK2$TZKNC N-K/II $ Malarstwo sztalugowe [(N.Z.) PPKZ 1982]';
        $this->assertEquals('Malarstwo sztalugowe [(N.Z.) PPKZ 1982]',Catalog::ExtractDescription($d_dLine));

    }
    public function testLoadDescriptions(Type $var = null)
    {
        $commonDir = 'resources/Norma3/Kat/';
        $catalogs = Catalog::LoadFrom($commonDir,CATALOG);//DESCRIPaRMS|BAZ_FILE_DIST
        $res = $catalogs['KNR  13-24']->getDescription();
        $expect = 'Roboty rem.i modern.maszyn i urządzeń,rurociągów technolog. oraz konstr.metal.elektrowni,elektrociep.i ciepłowni zaw. [Energobudowa wyd.II 1989, biuletyny do 9 1996]';
        $this->assertEquals($expect,$res);
    }
}
