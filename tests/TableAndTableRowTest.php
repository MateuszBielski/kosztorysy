<?php

namespace App\Tests;

use App\Entity\Catalog;
use App\Entity\Chapter;
use App\Entity\Circulation\Equipment;
use App\Entity\Circulation\Equipment_N_U;
use App\Entity\Circulation\Labor;
use App\Entity\Circulation\Material;
use App\Entity\Circulation\Material_N_U;
use App\Entity\ClTable;
use App\Entity\TableRow;
use PHPUnit\Framework\TestCase;
require_once('src/Service/Constants.php');


class TableAndTableRowTest extends TestCase
{
    public function testReadTableRowDecriptionFromOpLines()
    {
        // $table = new Table;
        $mainLine = '7$0.6$wymiana puszek podtynkowych o śr. $ 60 mm - przekrój przewodów do $ mm2 $Wymiana natynkowo-wtynkowych puszek szczękowych do przyborów$01';
        $subLine = '3$1$^$powyżej^$2.5^- 4 odgałęzienia$$ - 03';
        $tableRow = new TableRow;
        $tableRow->createCompoundDescription($mainLine,$subLine);
        $this->assertEquals('wymiana puszek podtynkowych o śr. powyżej 60 mm - przekrój przewodów do 2.5 mm2 - 4 odgałęzienia',$tableRow->getCompoundDescription());
    }
    public function testReadTableRowDecriptionFromOpFile()
    {
        $chapter = new Chapter;
        $opFile = fopen('resources/Norma3/Kat/2-02/2-02R1.OP','r');
        $chapter->LoadTablesWithDescriptionFromOP($opFile,DESCRIPaRMS);
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
    public function testCountRMSreadingFromChapterFiles()
    {
        $chapterFile = fopen('resources/Norma3/Kat/2-02/2-02R1.OP','r');
        $chapter = new Chapter;
        $chapter->LoadTablesWithDescriptionFromOP($chapterFile,DESCRIPaRMS);
        fclose($chapterFile);
        $tableRow = $chapter->getTables()[24]->getTableRows()[4];
        $R = $tableRow->getLabors();
        $M = $tableRow->getMaterials();
        $S = $tableRow->getEquipments();
        $this->assertEquals('231',count($R).count($M).count($S));

    }
    public function testCountRMSFromReadOPmainLine()
    {
        $tableRow = new TableRow;
        $mainIndices = 'szt.$2$4$0$2$3$55$56$57$1$';
        $subIndices = '';
        $tableRow->createCompoundRMSindices($mainIndices,$subIndices);
        $R = $tableRow->getLabors();
        $M = $tableRow->getMaterials();
        $S = $tableRow->getEquipments();
        $this->assertEquals('240',count($R).count($M).count($S));

    }
    public function testCountRMSFromReadOPsubLine()
    {
        $tableRow = new TableRow;
        $mainLine = 'm2$2$4$2$4$1$232$101$80$1$15$23$';
        $subLine = 'm2$2$3$2$4$1$232$80$1$15$23$';
        $tableRow->createCompoundRMSindices($mainLine,$subLine);
        $R = $tableRow->getLabors();
        $M = $tableRow->getMaterials();
        $S = $tableRow->getEquipments();
        $this->assertEquals('232',count($R).count($M).count($S));

    }
    public function testReadCircRelativeIndicesFromReadOPsubLine()
    {
        $tableRow = new TableRow;
        $mainLine = 'm2$2$4$2$4$1$232$101$80$1$15$23$';
        $subLine = 'm2$2$3$2$4$1$232$80$1$15$23$';
        $tableRow->createCompoundRMSindices($mainLine,$subLine);
        $m1 = $tableRow->getMaterials()[0];
        $m2 = $tableRow->getMaterials()[1];
        $this->assertEquals(232,$m1->getReadNameIndex());
        $this->assertEquals(80,$m2->getReadNameIndex());
    }
    public function testReadCircRelativeIndicesFromReadOPsubLine_optimizedTableRow()
    {
        $tableRow = new TableRow;
        $mainLine = 'm2$2$4$2$4$1$232$101$80$1$15$23$';
        $subLine = 'm2$2$3$2$4$1$232$80$1$15$23$';
        $tableRow->createCompoundRMSindices_optimized($mainLine,$subLine);
        $m1 = $tableRow->getMaterials()[0];
        $m2 = $tableRow->getMaterials()[1];
        $this->assertEquals(232,$m1->getReadNameIndex());
        $this->assertEquals(80,$m2->getReadNameIndex());
    }
    // public function testCropLineToDescriptionAndSet()
    public function testSetAfterSplitLineIntoDescriptionAndIndices_TableRow()
    {
        $tableRow = new TableRow;
        // $subLine = '56$0.15$próba wodna szczelności sieci wodociągowych z rur typu HOBAS, PCW, PVC, PE, PEHD o śr.nominalnej $[..]$ mm$$04 $200m -1 prób.$1$22$3$1$232$233$234$235$243$236$240$246$247$30$32$12$13$35$36$37$38$39$238$241$242$1$2$3$44$';
        $subLine = '59$1$$$^$$ - 05 $szt.$2$3$2$4$1$8$80$1$15$23$';
        // $expected = '56$0.15$próba wodna szczelności sieci wodociągowych z rur typu HOBAS, PCW, PVC, PE, PEHD o śr.nominalnej $[..]';
        $expecDesc ='59$1$$$^$$ - 05';
        $expecInd = 'szt.$2$3$2$4$1$8$80$1$15$23';
        $tableRow->SetAfterSplitLineIntoDescriptionAndIndices($subLine);
        $this->assertEquals($expecDesc,$tableRow->getSubDescription());
        $this->assertEquals($expecInd,$tableRow->getSubIndices());
    }
    public function testSetAfterSplitLineIntoDescriptionAndIndices_ClTable()
    {
        $table = new ClTable;
        $line = '36$0.6$dachy z wiazarów deskowych z tarcicy nasyc.o rozp.$$$$05 $m2$3$4$2$13$4$1$51$80$186$1$15$23$';
        $expecDesc = '36$0.6$dachy z wiazarów deskowych z tarcicy nasyc.o rozp.$$$$05';
        $expecInd = 'm2$3$4$2$13$4$1$51$80$186$1$15$23';
        $table->SetAfterSplitLineIntoDescriptionAndIndices($line);
        $this->assertEquals($expecDesc,$table->getMainDescription());
        $this->assertEquals($expecInd,$table->getMainIndices());
    }
    public function testRemoveZeroCirculations(Type $var = null)
    {
        $tableRow = new TableRow;
        $subIndices = 'elem.$4$7$3$10$9$11$1$241$746$182$187$568$1$65$18$43$23$';
        $tableRow->createCompoundRMSindices($subIndices,'');
        $values = range(1,15);
        $values[2] = 0;
        $values[5] = 0;
        $values[7] = 0;
        $values[12] = 0;
        $tableRow->setValuesToCirculations($values);
        $this->assertEquals(10,count($tableRow->getCirculations()));
    }
    public function testSetCircNameAndUnit()
    {
        // $catalog = new Catalog;
        // $chapter = new Chapter;
        // $table = new ClTable;
        $materialNamed = new Material_N_U;
        $materialNamed->setName('belki drewniane');
        $equipmentNamed = new Equipment_N_U;
        $equipmentNamed->setName('ciężarówka');
        $nameAndUnitArray = array();
        $nameAndUnitArray['M'][245] = $materialNamed;
        $nameAndUnitArray['S'][21] = $equipmentNamed;
        $tableRow = new TableRow;
        $material = new Material;
        $material->setReadNameIndex(245);
        $tableRow->addMaterial($material);

        $equipment = new Equipment;
        $equipment->setReadNameIndex(21);
        $tableRow->addEquipment($equipment);
        $tableRow->SelectNameAndUnitToCirculations($nameAndUnitArray);

        $this->assertEquals('belki drewniane',$tableRow->getMaterials()[0]->getName());
        $this->assertEquals('ciężarówka',$tableRow->getEquipments()[0]->getName());
    }
    public function testCirculationReadGroupOptionsNumber()
    {
        $tableRow = new TableRow;
        $subIndices = 'm3$7$6$11$1028$1029$1030$13$2004$2004$2004$242$45$4$80$243$1$3024$3024$3024$4019$4019$4019$25$42$5026$5026$5026$';
        $tableRow->createCompoundRMSindices($subIndices,'');
        $labors = $tableRow->getLabors();
        $equipments = $tableRow->getEquipments();
        $this->assertEquals(2,$labors[5]->getGroupNumber());
        $this->assertEquals(4,$equipments[4]->getGroupNumber());
    }
    public function testTableExtractMyNumber()
    {
        $table = new ClTable;
        $textLine = "****   TABLICA  09    ****";
        $this->assertEquals(9,$table->ExtractMyNumber($textLine));
    }
    public function testTableOwnNumber()
    {
        $chapter = new Chapter;
        $opFile = fopen('resources/Norma3/Kat/2-02/2-02R2.OP','r');
        $chapter->LoadTablesWithDescriptionFromOP($opFile,TABLE_ROW);
        fclose($opFile);
        $table = $chapter->getTables()[48];
        $this->assertEquals(87,$table->getMyNumber());
    }
    public function testTableRowExtractMyNumber()
    {
        $text = " - 06 ";
        $tableRow = new TableRow;
        $this->assertEquals(6,$tableRow->ExtractMyNumber($text));
    }
    public function testTableRowOwnNumber()
    {
        $chapter = new Chapter;
        $opFile = fopen('resources/Norma3/Kat/2-02/2-02R2.OP','r');
        $chapter->LoadTablesWithDescriptionFromOP($opFile,DESCRIPaRMS);
        fclose($opFile);
        $table = $chapter->getTables()[12];
        $tableRow = $table->getTableRows()[11];
        $this->assertEquals(12,$tableRow->getMyNumber());
    }
    public function testTableGetFullName()
    {
        $catalog = new Catalog;
        $catalog->setName('KNR 2-02');
        $chapter = new Chapter;
        $chapter->setMyCatalog($catalog);
        $chapter->setName('Rozdział 05');
        $table = new ClTable;
        $table->setMyChapter($chapter);
        $table->setMyNumber(3);
        $this->assertEquals('KNR 2-02 0503',$table->getFullName());
    }
    public function testTableRowGetFullName()
    {
        $catalog = new Catalog;
        $catalog->setName('KNR 2-02');
        $chapter = new Chapter;
        $chapter->setMyCatalog($catalog);
        $chapter->setName('Rozdział 05');
        $table = new ClTable;
        $table->setMyChapter($chapter);
        $table->setMyNumber(3);
        $tableRow = new TableRow;
        $tableRow->setMyTable($table);
        $tableRow->setMyNumber(2);
        $this->assertEquals('KNR 2-02 0503-02',$tableRow->getFullName());
    }
    public function testTableGetDescription()
    {
        $textLine = '5$0.2$Krycie dachów papą termozgrzewalną dkd na podłożu$ betonowym$,$ drewnianym$27 $m2$2$11$3$2$1$2$3$4$5$6$7$8$9$10$11$1$2$3$4$';
        $table = new ClTable;
        $table->SetAfterSplitLineIntoDescriptionAndIndices($textLine);
        $this->assertEquals('Krycie dachów papą termozgrzewalną dkd na podłożu betonowym, drewnianym',$table->getDescription());
        //sprawdzić opis dla tablicy KNR 5-16 0204
    }
    public function testTableRowGetUnit()
    {
        $tableRow = new TableRow;
        $mainLine = 'm2$2$4$2$4$1$232$101$80$1$15$23$';
        $subLine = 'm2$2$3$2$4$1$232$80$1$15$23$';
        $tableRow->createCompoundRMSindices($mainLine,$subLine);
        $this->assertEquals('m2',$tableRow->getUnit());
    }
    public function testGenerateValuesForTwigCostTable()
    {
        $catFile = '/var/www/html/norma/resources/Norma3/Kat/KNW3';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile,DESCRIPaRMS|BAZ_FILE_DIST);
        
        //KNNR-W 3 0201-03
        $tr = $catalog->getMyChapters()['Rozdział 02']->getTables()[0]->getTableRows()[2];
        $stringExpected = "--R--\n";
        $stringExpected .= "robotnicy30.1r-g\n";
        $stringExpected .= "--M--\n";
        $stringExpected .= "beton zwykły z kruszywa naturalnego1.01m3\n";
        $stringExpected .= "deski iglaste obrzynane 19-25 mm kl.III0.014m3\n";
        $stringExpected .= "deski iglaste obrzynane 28-45 mm kl.III0.008m3\n";
        $stringExpected .= "materiały pomocnicze2%\n";
        $stringExpected .= "--S--\n";
        $stringExpected .= "samochód samowyładowczy 5 t0.63m-g\n";
        $stringResult = '';
        foreach($tr->GenerateValuesForTwigCostTable() as $row)
        {
            foreach($row as $td)
            {
                $stringResult .= $td;
            }
            $stringResult .="\n";
        }
        $this->assertEquals($stringExpected,$stringResult);
    }
    
}