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
        $catalogs = Catalog::LoadFrom($commonDir);
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
    
    //konkretna treść rozdziału po załadowaniu wszytskiego
    //get file names/paths from random cat directory
}
