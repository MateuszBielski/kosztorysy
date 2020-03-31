<?php

namespace App\Tests;

use App\Entity\Chapter;
use App\Entity\TableRow;
use PHPUnit\Framework\TestCase;

class TableRowTest extends TestCase
{
    public function testReadTableRowDecriptionFromOpLines()
    {
        // $table = new Table;
        $mainLine = '7$0.6$wymiana puszek podtynkowych o '.chr(0x9e).'r. $ 60 mm - przekr'.chr(0xa2).'j przewod'.chr(0xa2).'w do $ mm2 $Wymiana natynkowo-wtynkowych puszek szcz'.chr(0x91).'kowych do przybor'.chr(0xa2).'w$01 $szt.$2$4$0$2$3$55$56$57$1$';
        $subLine = '3$1$^$powy'.chr(0xa7).'ej^$2.5^- 4 odga'.chr(0x92).chr(0x91).'zienia$$ - 03 $';
        $tableRow = new TableRow;
        $tableRow->createCompoundDescriptionAndRMS($mainLine,$subLine);
        $this->assertEquals('wymiana puszek podtynkowych o śr. powyżej 60 mm - przekrój przewodów do 2.5 mm2 - 4 odgałęzienia',$tableRow->getCompoundDescription());
    }
    public function testReadTableRowDecriptionFromOpFile()
    {
        $chapter = new Chapter;
        $opFile = fopen('resources/Norma3/Kat/2-02/2-02R1.OP','r');
        $chapter->LoadTablesWithDescriptionFromOP($opFile);
        fclose($opFile);
        $tables = $chapter->getTables();
        $this->assertEquals(35,count($tables));
        $tableRows6 = $tables[6]->getTableRows();
        $tableRow8 = $tableRows6[7];
        // foreach($tableRows6 as $row){
        //     echo "\n".$row->getCompoundDescription();
        // }
        $this->assertEquals(12,count($tableRows6));
        $this->assertEquals('Ściany budynków jednokond.o wys.pow. 4.5m z bloczków z bet.komórkow.gr.37cm',$tableRow8->getCompoundDescription());
    }
    public function testCountRMSFromReadOPmainLine()
    {
        $tableRow = new TableRow;
        $mainLine = '7$0.6$wymiana puszek podtynkowych o '.chr(0x9e).'r. $ 60 mm - przekr'.chr(0xa2).'j przewod'.chr(0xa2).'w do $ mm2 $Wymiana natynkowo-wtynkowych puszek szcz'.chr(0x91).'kowych do przybor'.chr(0xa2).'w$01 $szt.$2$4$0$2$3$55$56$57$1$';
        $subLine = '3$1$^$powy'.chr(0xa7).'ej^$2.5^- 4 odga'.chr(0x92).chr(0x91).'zienia$$ - 03 $';
        $tableRow->createCompoundDescriptionAndRMS($mainLine,$subLine);
        $R = $tableRow->getLabors();
        $M = $tableRow->getMaterials();
        $S = $tableRow->getEquipments();
        $this->assertEquals('240',count($R).count($M).count($S));

    }
    public function testCountRMSFromReadOPsubLine()
    {
        $tableRow = new TableRow;
        $mainLine = '75$0.5$deskowanie polaci dachowych $olacenie polaci dachowych latami 38x50mm,$okienko \'wole oko\'$ z tarcicy nasyc.$10 $m2$2$4$2$4$1$232$101$80$1$15$23$';
        $subLine = '55$1$^$$$^$ - 01 $m2$2$3$2$4$1$232$80$1$15$23$';
        $tableRow->createCompoundDescriptionAndRMS($mainLine,$subLine);
        $R = $tableRow->getLabors();
        $M = $tableRow->getMaterials();
        $S = $tableRow->getEquipments();
        $this->assertEquals('232',count($R).count($M).count($S));

    }
}