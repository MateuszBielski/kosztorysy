<?php

namespace App\Tests;

use App\Entity\Circulation\CirculationNameAndUnit;
use App\Entity\Circulation\Equipment_N_U;
use App\Entity\Circulation\Labor_N_U;
use App\Entity\Circulation\Material_N_U;

use App\Entity\PriceList;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PriceListTest extends KernelTestCase
{
    private $entityManager;
    private $repCirNU;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $doctrine = $kernel->getContainer()
        ->get('doctrine');

        $this->entityManager = $doctrine->getManager();
        $this->repCirNU = $doctrine->getRepository(CirculationNameAndUnit::class);
    }
    public function testCreateRandomPrices()
    {
        $minPrice = 0.95;
        $maxPrice = 301.34;
        $circulationNUs = array();
        $circulationNUs[] = new Labor_N_U;
        $circulationNUs[] = new Labor_N_U;
        $circulationNUs[] = new Material_N_U;
        $circulationNUs[] = new Material_N_U;
        $circulationNUs[] = new Material_N_U;
        $circulationNUs[] = new Equipment_N_U;
        $circulationNUs[] = new Equipment_N_U;
        $priceList = new PriceList;
        $priceList->CreateRandomPrices($circulationNUs,$minPrice,$maxPrice);
        $this->assertEquals(7,count($priceList->getPrices()));
        foreach($priceList->getPrices() as $ip)
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
