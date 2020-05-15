<?php

namespace App\Tests;

use App\Entity\TableRow;
use App\Entity\Circulation\Equipment;
use App\Entity\Circulation\Labor;
use App\Entity\Circulation\Material;
use App\Entity\CostItem;
use PHPUnit\Framework\TestCase;

class CostItemTest extends TestCase
{
    public function testGenerateValuesForTwigCostTable()
    {
        $av_R = array(1.23,4.2);
        $av_M = array(2.25,0.03,1.03);
        $av_S = array(2.03,2.5);
        $tr = new TableRow;
        // $a_R = array();
        // $a_M = array();
        // $a_S = array();
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
        $ci->setTableRow($tr);
        $ci->setSurvey(24.1);
        $this->assertEquals(145,count($ci->GenerateValuesForTwigCostTable()));

    }
}