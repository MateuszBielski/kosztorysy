<?php

namespace App\Tests;

use App\Entity\Catalog;
use App\Entity\Chapter;
use PHPUnit\Framework\TestCase;
require_once('src/Service/Constants.php');

class ChapterTest extends TestCase
{
    public function testReadNameFromTextLine()
    {
        $chapter = new Chapter;

        $chapter->readFrom('0$( Rozdzia'.chr(0x92).' 06 ) * Izolacje$ 06$2-02r6');
        $this->assertEquals('Rozdział 06',$chapter->getName());
    }
    public function testReadDescriptionFromTextLine()
    {
        $chapter = new Chapter;
        $chapter->readFrom('0$( Rozdzia'.chr(0x92).' 02 ) * Systemy sp'.chr(0x92).'ukuj'.chr(0x86).'ce$ 02$2g15r2');
        $this->assertEquals('Systemy spłukujące',$chapter->getDescription());
    }
    public function testOneChapterContentAfterLoad()
    {   
        $commonDir = 'resources/Norma3/Kat/';
        $catalogs = Catalog::LoadFrom($commonDir,CHAPTER);//,DESCRIPaRMS|OPTIMIZE_TR CHAPTER
        $res = $catalogs['KNR 2-15/G']->getMyChapters()['Rozdział 06']->getDescription();
        $this->assertEquals('System wodociągowy Geberit Mepla',$res);
        /*
        różne czasy wykonania łącznie 6 testów, na całym folderze z 258 katalogami dla poszczególnych poziomów odczytów:
        CATALOG: 400 ms 4MB
        CHAPTER: 395(?) ms 4MB
        TABLE:   527 s 12MB
        TABLE_ROW 3.48 S 70MB
        DESCRIPaRMS 25.29 318MB
        nie potwierdza to przypuszczenia, że tworzenie dużej ilości Encji (np TableRow) 
        pochałania dużo zasobów, lecz że zasobożerna jest funkcja createCompoundDescriptionAndRMS, 
        
        */
    }
    //test wykonuje się ponad pół minuty
    public function _testOneChapterContentAfterLoad_optimizedTableRow()
    {   
        $commonDir = 'resources/Norma3/Kat/';
        $catalogs = Catalog::LoadFrom($commonDir,DESCRIPaRMS|OPTIMIZE_TR);//,DESCRIPaRMS|OPTIMIZE_TR CHAPTER
        $res = $catalogs['KNR 2-15/G']->getMyChapters()['Rozdział 06']->getDescription();
        $this->assertEquals('System wodociągowy Geberit Mepla',$res);
        /*
        czas wykonania dla:
         DESCRIPaRMS|OPTIMIZE_TR 33.07 seconds, Memory: 402.00 MB
         DESCRIPaRMS            35.97 seconds, Memory: 422.00 MB
         mały zysk... czyli jest jakieś drugie wąskie gardło zapewne w GiveValuesToCirculations
        */
    }
    public function testCountTables()
    {
        $chapter = new Chapter;
        //$chapter->setMyDetailsFileBaseName('2g15r1');
        $file = fopen('resources/Norma3/Kat/2G15/2G15R1.op','r');
        $chapter->LoadTablesWithDescriptionFromOP($file);
        fclose($file);
        $this->assertEquals(5,count($chapter->getTables()));
    }
    public function testCountTablesAfterReadingTextLine()
    {
        $catalog = new Catalog;
        $chapter = new Chapter;
        $chapter->setMyCatalog($catalog);
        $catalog->dirPath = 'resources/Norma3/Kat/2G15';
        $chapter->readFrom('0$( Rozdzia'.chr(0x92).' 01 ) * Systemy instalacyjne$ 01$2g15r1',TABLE);
        $this->assertEquals(5,count($chapter->getTables()));
    }
    public function testReadCircValuesFromNORfile()
    {
        $chapter = new Chapter;
        $norFile = fopen('resources/Norma3/Kat/2-02/2-02R5.NOR','r');
        $chapter->LoadCircValuesFromNOR($norFile);
        fclose($norFile);
        $res = $chapter->getCircValues()[4][1];
        $this->assertEquals(0.0277,$res);
    }
    public function testGiveValuesToCirculations()
    {
        $chapterFilePath = 'resources/Norma3/Kat/0-39/0-39R1.';
        $chapter = new Chapter;

        $norFile = fopen($chapterFilePath.'NOR','r');
        $chapter->LoadCircValuesFromNOR($norFile);
        fclose($norFile);

        $OpFile = fopen($chapterFilePath.'OP','r');
        $chapter->LoadTablesWithDescriptionFromOP($OpFile,DESCRIPaRMS);
        fclose($OpFile);
        $tableRow17_3 = $chapter->getTables()[17]->getTableRows()[3];

        $chapter->GiveValuesToCirculations();
        $material1 = $tableRow17_3->getMaterials()[1];
        $this->assertEquals(2.5,$material1->getValue());
    }
    public function testGiveValuesToCirculations_optimizedTableRow()
    {
        $chapterFilePath = 'resources/Norma3/Kat/0-39/0-39R1.';
        $chapter = new Chapter;

        $norFile = fopen($chapterFilePath.'NOR','r');
        $chapter->LoadCircValuesFromNOR($norFile);
        fclose($norFile);

        $OpFile = fopen($chapterFilePath.'OP','r');
        $chapter->LoadTablesWithDescriptionFromOP($OpFile,DESCRIPaRMS|OPTIMIZE_TR);
        fclose($OpFile);
        $tableRow17_3 = $chapter->getTables()[17]->getTableRows()[3];

        $chapter->GiveValuesToCirculations();
        $material1 = $tableRow17_3->getMaterials()[1];
        $this->assertEquals(2.5,$material1->getValue());
    }
    public function testGetFullName()
    {
        $catalog = new Catalog;
        $catalog->setName('KNR 2-02');
        $chapter = new Chapter;
        $chapter->setMyCatalog($catalog);
        $chapter->setName('Rozdział 05');
        $this->assertEquals('KNR 2-02 05',$chapter->getFullName());
        
    }
}
