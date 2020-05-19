<?php

namespace App\Tests;

use App\Entity\Catalog;
use App\Entity\TableRow;
use App\Entity\Circulation\Equipment;
use App\Entity\Circulation\Labor;
use App\Entity\Circulation\Material;
use App\Entity\CostItem;
use PHPUnit\Framework\TestCase;
require_once('src/Service/Constants.php');

class CostItemTest extends TestCase
{
    public function testInitializeCostItem()
    {
        $catFile = '/var/www/html/norma/resources/Norma3/Kat/KNW3';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile,DESCRIPaRMS|BAZ_FILE_DIST);
        
        //KNNR-W 3 0201-03
        $tr = $catalog->getMyChapters()['Rozdział 02']->getTables()[0]->getTableRows()[2];
        
        $ci = new CostItem;
        $ci->Initialize($tr);
        $this->assertEquals('KNNR-W 3 0201-03',$ci->getFullName());
        $this->assertEquals(4,count($ci->getMaterials()));
        $priceLabor = $ci->getLabors()[0];
        $this->assertEquals(30.1,$priceLabor->getValue());
    }
    public function testGenerateValuesForTwigCostTable()
    {
        $av_R = array(1.23,4.2);
        $av_M = array(2.25,0.03,1.03);
        $av_S = array(2.03,2.5);
        $tr = new TableRow;
        foreach($av_R as $v){
            $c = new Labor();
            $c->setValue($v);
            $tr->addLabor($c);
        }
        foreach($av_M as $v){
            $c = new Material();
            $c->setValue($v);
            $tr->addMaterial($c);
        }
        foreach($av_S as $v){
            $c = new Equipment();
            $c->setValue($v);
            $tr->addEquipment($c);
        }
        $ci = new CostItem;
        $ci->Initialize($tr);
        $ci->setSurvey(24.1);
        $stringExpected = 'robocizna1.234.2materiały2.250.031.03sprzęt2.032.5';
        $stringResult = '';
        foreach($ci->GenerateValuesForTwigCostTable() as $row)
        {
            foreach($row as $td)
            {
                $stringResult .= $td;
            }
        }
        $this->assertEquals($stringExpected,$stringResult);
    }
}
