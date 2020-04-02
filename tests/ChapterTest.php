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
        $catalogs = Catalog::LoadFrom($commonDir,TABLE_ROW);//,CATALOG
        $res = $catalogs['KNR 2-15/G']->getMyChapters()['Rozdział 06']->getDescription();
        $this->assertEquals('System wodociągowy Geberit Mepla',$res);
        /*
        różne czasy wykonania łącznie 6 testów, na całym folderze z 258 katalogami dla poszczególnych poziomów odczytów:
        CATALOG: 414 ms 4MB
        CHAPTER: 416 ms 4MB
        TABLE:   9.03 s 12MB
        TABLE_ROW 28.3 S 318MB
        potwierdza to przypuszczenie, że tworzenie dużej ilości Encji (np TableRow) pochałania dużo zasobów
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
}
