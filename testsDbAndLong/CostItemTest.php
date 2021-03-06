<?php

namespace App\Tests;

use App\Entity\Catalog;
use App\Entity\TableRow;
use App\Entity\Circulation\Equipment;
use App\Entity\Circulation\Labor;
use App\Entity\Circulation\Material;
use App\Entity\CostItem;
use App\Entity\Circulation\CirculationNameAndUnit;
use App\Entity\Circulation\Equipment_N_U;
use App\Entity\Circulation\Labor_N_U;
use App\Entity\Circulation\Material_N_U;
use App\Entity\ItemPrice;
use App\Repository\ItemPriceRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

require_once('src/Service/Constants.php');



class PriceListTest extends KernelTestCase
{

    protected $entityManager;
    protected $repItPr;
    // protected $repItPrMock;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $doctrine = $kernel->getContainer()
            ->get('doctrine');

        $this->entityManager = $doctrine->getManager();
        $this->repItPr = $doctrine->getRepository(ItemPrice::class);
    }
    public function testInitializeCostItem()
    {
        $catFile = '/var/www/html/norma/resources/Norma3/Kat/KNW3';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile, DESCRIPaRMS | BAZ_FILE_DIST);

        //KNNR-W 3 0201-03
        $tr = $catalog->getMyChapters()['Rozdział 02']->getTables()[0]->getTableRows()[2];

        $ci = new CostItem;
        $ci->Initialize($tr);
        $this->assertEquals('KNNR-W 3 0201-03', $ci->getFullName());
        $this->assertEquals(4, count($ci->getMaterials()));
        $priceLabor = $ci->getLabors()[0];
        $this->assertEquals(30.1, $priceLabor->getValue());
    }
    //po uzyciu bloków twiga nie jest to potrzebna funkcjonalność
    public function _testGenerateValuesForTwigCostTable()
    {
        $av_R = array(1.23, 4.2);
        $av_M = array(2.25, 0.03, 1.03);
        $av_S = array(2.03, 2.5);
        $tr = new TableRow;
        $id = 9;
        $setting = function (&$c, $v, &$id, $cnuClass) {
            $c->setValue($v);
            $cnu = new $cnuClass;
            echo "\nid" . $id;
            $cnu->setId($id--);
            $c->setNameAndUnit($cnu);
        };
        foreach ($av_R as $v) {
            $c = new Labor();
            $setting($c, $v, $id, Labor_N_U::class);
            $tr->addLabor($c);
        }
        foreach ($av_M as $v) {
            $c = new Material();
            $setting($c, $v, $id, Material_N_U::class);
            $tr->addMaterial($c);
        }
        foreach ($av_S as $v) {
            $c = new Equipment();
            $setting($c, $v, $id, Equipment_N_U::class);
            $tr->addEquipment($c);
        }
        $ci = new CostItem;
        $ci->Initialize($tr);
        $ci->setSurvey(24.1);
        $stringExpected = '--R--01.2304.2--M--02.2500.0301.03--S--02.0302.5';
        $stringResult = '';
        $expectedKeys = '';
        foreach ($ci->GenerateValuesForTwigCostTable() as $k => $row) {
            $expectedKeys .= $k;
            echo "\n" . $k;
            foreach ($row as $td) {
                $stringResult .= $td;
            }
        }
        $this->assertEquals($stringExpected, $stringResult);
    }
    public function testUpdatePricesFromRepository()
    {
        $returns = array();
        $returns[19] = 23.5;
        $returns[21] = 24.6;
        $repItPrMock = $this->createMock(ItemPriceRepository::class);
        $repItPrMock->expects($this->any())
            ->method('findByPriceListAndCircNU')->willReturn($returns);

        $tableRow = new TableRow;
        $lab = new Labor;
        $labNU = new Labor_N_U;
        $labNU->setId(19);
        $lab->setNameAndUnit($labNU);
        $tableRow->addLabor($lab);
        $mat = new Material;
        $matNU = new Material_N_U;
        $matNU->setId(21);
        $mat->setNameAndUnit($matNU);
        $tableRow->addMaterial($mat);
        // $costItem->Initialize($tableRow);

        $costItem = new CostItem;
        $costItem->Initialize($tableRow);
        $costItem->UpdatePricesFrom($repItPrMock);
        $this->assertEquals(23.5, $costItem->getLabors()[0]->getPriceValue());
    }
    public function testReadRepository(Type $var = null)
    {
        $cirIDS = array(8,30,21,85);
        $results = $this->repItPr->findByPriceListAndCircNU('ceny losowe1259',$cirIDS);
        // foreach($results as $res)echo "\n".$res;
        print_r($results);
        // foreach($results as $k => $res){
        //     echo "\nk: ".$k.", val: ".$res;
        // }
    }
}
