<?php

namespace App\Tests;

use App\Entity\Circulation\CirculationNameAndUnit;
use App\Entity\Circulation\Equipment_N_U;
use App\Entity\Circulation\Labor_N_U;
use App\Entity\Circulation\Material_N_U;
use App\Entity\ItemPrice;
use App\Entity\PriceList;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PriceListTest extends KernelTestCase
{
    private $entityManager;
    private $repCirNU;

    protected function setUp()
    {
        /*
        $kernel = self::bootKernel();
        $doctrine = $kernel->getContainer()
        ->get('doctrine');

        $this->entityManager = $doctrine->getManager();
        $this->repCirNU = $doctrine->getRepository(CirculationNameAndUnit::class);
        */
    }
    public function testCreateRandomPrices()
    {
        $minPrice = 0.95;
        $maxPrice = 301.34;
        $circulationNUs = [];
        $circulationNUs[] = new Material_N_U;
        $circulationNUs[] = new Material_N_U;
        $circulationNUs[] = new Material_N_U;
        $circulationNUs[] = new Equipment_N_U;
        $circulationNUs[] = new Equipment_N_U;
        $priceList = new PriceList;
        $randomPrices = $priceList->CreateRandomPrices($circulationNUs,$minPrice,$maxPrice);
        $this->assertEquals(5,count($randomPrices));
        foreach($randomPrices as $ip)
        {
            $price = $ip->getPriceValue()/100;
            $this->assertTrue($price >= $minPrice && $price <= $maxPrice);
        }
    }
    public function testCreatedPricesHaveCorrectName()
    {
        $circulationNUs = [];
        $material = new Material_N_U;
        $material->setName('ekspres');
        $circulationNUs[] = $material;
        $priceList = new PriceList;
        $randomPrices = $priceList->CreateRandomPrices($circulationNUs,0.5,0.7);
        $this->assertEquals($material->getName(),$randomPrices[0]->getNameAndUnit()->getName());
    }
    public function testCreatedPricesBelongsKnowsTheirList()
    {
        $circulationNUs = [];
        $material = new Material_N_U;
        // $material->setName('ekspres');
        $circulationNUs[] = $material;
        $priceList = new PriceList;
        $priceList->setName('ceny1');
        $randomPrices = $priceList->CreateRandomPrices($circulationNUs,0.5,0.7);
        $this->assertEquals('ceny1',$randomPrices[0]->getPriceList()->getName());
    }
    public function testAssignRandomPrices()
    {
        $prices = [];
        for($i = 0 ; $i < 10 ; $i++)
        {
            $prices[] = new ItemPrice;
        }
        $minPrice = 5;
        $maxPrice = 30;
        $priceList = new PriceList;
        $priceList->AssignRandomPrices($prices,$minPrice,$maxPrice);
        foreach($prices as $ip)
        {
            $price = $ip->getPriceValue()/100;
            $this->assertTrue($price >= $minPrice && $price <= $maxPrice);
        }
    }
    //ponizszy test wychodzi tylko dla bazy danych nietestowej, czyli zawierającej odczytane katalogi
    public function _testCreateRandomPricesFromRepository()
    {
        // $circulations = array_slice($this->repCirNU->findAll(),0,1000);
        $circulationNUs = $this->repCirNU->findAll();
        $priceList = new PriceList;
        $priceList->CreateRandomPrices($circulationNUs,100,1000);
        $this->assertEquals(22008,$priceList->getAmonutOfPrices());
    }
}
