<?php

namespace App\Tests;

// use PHPUnit\Framework\TestCase;

use App\Entity\Circulation\Labor;
use App\Entity\Circulation\Material;
use App\Entity\Kosztorys;
use App\Entity\PozycjaKosztorysowa;
use App\Entity\TableRow;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class PozycjaKosztorysowaTest extends KernelTestCase
{
    private $entityManager;
    private $conn;
    private $repTableRow;
    protected function setUp()
    {
        /*
        $kernel = self::bootKernel();
        $doctrine = $kernel->getContainer()
        ->get('doctrine');

        $this->repTableRow = $doctrine->getRepository(TableRow::class);
        $this->entityManager = $doctrine->getManager();
        $this->conn = $this->entityManager->getConnection();
        */
    }
    
    public function testCreateDependecyForRenderAndTest_obmiar()
    {
        $pozycja = new PozycjaKosztorysowa;
        $params = [
            'obmiar'=>142,
        ];
        $pozycja->CreateDependecyForRenderAndTest($params);
        $this->assertEquals(142,$pozycja->getObmiar());
    }
    public function testCreateDependecyForRenderAndTest_Jednostka()
    {
        $pozycja = new PozycjaKosztorysowa;
        $params = [
            'unit'=>'szt',
        ];
        $pozycja->CreateDependecyForRenderAndTest($params);
        $this->assertEquals('szt',$pozycja->Jednostka());
    }
    public function testCreateDependecyForRenderAndTest_getId()
    {
        $pozycja = new PozycjaKosztorysowa;
        $params = [
            'pk_id'=>11,
        ];
        $pozycja->CreateDependecyForRenderAndTest($params);
        $this->assertEquals(11,$pozycja->getId());
    }
    public function testZmienObmiarIprzelicz_pojedynczyMaterial()
    {
        $pozycja = new PozycjaKosztorysowa;
        $material = new Material;
        $material->setValue(0.45);
        $material->setPrice(124);
        $tableRow = new TableRow;
        $tableRow->addMaterial($material);
        $pozycja->setPodstawaNormowa($tableRow);
        $pozycja->ZmienObmiarIprzelicz(132);
        $this->assertEquals(73.6560,$material->getKoszt());
    }
    public function testPrzeliczDlaAktualnegoObmiaru()
    {
        $tabl = [
            'value'=>[0.5,0.35,21,4],
            'name'=>['name1','name3','name5','name4'],
            'unit'=>['a','b','c','m'],
            'price_value'=>[10,42,53,34]
        ];
        $param = [];
        $tr = new TableRow;
        $param['materials'] = $tr->KonwertujTabliceParametrowWzgodzieZrepo($tabl);
        $tr->CreateDependecyForRenderAndTest($param);
        $pozycja = new PozycjaKosztorysowa;
        $pozycja->setObmiar(12);
        $pozycja->setPodstawaNormowa($tr);
        $pozycja->PrzeliczDlaAktualnegoObmiaru();
        $this->assertEquals(0.6,$tr->getMaterials()[0]->getKoszt());
    }
    public function testZaladowaniePodstawyNormowejUstawiaCeneDlaRobociznyZkosztorysu()
    {
        $kosztorys  = new Kosztorys;
        $kosztorys->setRoboczogodzina(2341);
        $pozycjaKosztorysowa = new PozycjaKosztorysowa;
        $pozycjaKosztorysowa->setKosztorys($kosztorys);
        
        $robotnicy = new Labor;
        $tr = new TableRow;
        $tr->addLabor($robotnicy);
        $pozycjaKosztorysowa->setPodstawaNormowa($tr);
        $this->assertEquals(23.41,$robotnicy->getPriceDivBy100());

    }
    public function testDlaProcentowychWartosciWyliczaNakladZeSwojejKategorii_materialy()
    {
        $tabl = [
            'value'=>[1.5,1,1,1],
            'name'=>['name1','name3','name5','name4'],
            'unit'=>['%','b','c','m'],
            'price_value'=>[10,100,100,100]
        ];
        $param = [];
        $tr = new TableRow;
        $param['materials'] = $tr->KonwertujTabliceParametrowWzgodzieZrepo($tabl);
        $tr->CreateDependecyForRenderAndTest($param);
        $pozycja = new PozycjaKosztorysowa;
        $pozycja->setObmiar(5);
        $pozycja->setPodstawaNormowa($tr);
        $pozycja->PrzeliczDlaAktualnegoObmiaru();
        $this->assertEquals(0.225,$tr->getMaterials()[0]->getKoszt());
    }
    public function testProcentyWyliczaDlaPozostalychKosztowJesliNieMaWswojejKategorii()
    {
        $tabl1 = [
            'value'=>[1.6],
            'name'=>['name1'],
            'unit'=>['%'],
            'price_value'=>[10]
        ];
        $tabl2 = [
            'value'=>[1,1,1,1],
            'name'=>['name1','name3','name5','name4'],
            'unit'=>['m','b','c','m'],
            'price_value'=>[100,100,100,100]
        ];
        $param = [];
        $tr = new TableRow;
        $param['materials'] = $tr->KonwertujTabliceParametrowWzgodzieZrepo($tabl1);
        $param['labors'] = $tr->KonwertujTabliceParametrowWzgodzieZrepo($tabl2);
        $tr->CreateDependecyForRenderAndTest($param);
        $pozycja = new PozycjaKosztorysowa;
        $pozycja->setObmiar(5);
        $pozycja->setPodstawaNormowa($tr);
        $pozycja->PrzeliczDlaAktualnegoObmiaru();
        $this->assertEquals(0.32,$tr->getMaterials()[0]->getKoszt());
    }
    public function testDlaProcentowychWartosciWyliczaNakladZeSwojejKategorii_sprzet()
    {
        $tabl = [
            'value'=>[1,1,1.5,1],
            'name'=>['name1','name3','name5','name4'],
            'unit'=>['r','b','%','m'],
            'price_value'=>[10,100,100,100]
        ];
        $param = [];
        $tr = new TableRow;
        $param['equipments'] = $tr->KonwertujTabliceParametrowWzgodzieZrepo($tabl);
        $tr->CreateDependecyForRenderAndTest($param);
        $pozycja = new PozycjaKosztorysowa;
        $pozycja->setObmiar(10);
        $pozycja->setPodstawaNormowa($tr);
        $pozycja->PrzeliczDlaAktualnegoObmiaru();
        print('xx'.count($tr->getEquipments()));
        $this->assertEquals(0.315,$tr->getEquipments()[2]->getKoszt());
    }
}
