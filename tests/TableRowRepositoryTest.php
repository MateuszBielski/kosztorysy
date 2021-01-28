<?php

namespace App\Tests;

// use PHPUnit\Framework\TestCase;

use App\Entity\TableRow;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class TableRowRepositoryTest extends KernelTestCase
{
    private $entityManager;
    private $conn;
    private $repTableRow;
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $doctrine = $kernel->getContainer()
        ->get('doctrine');

        $this->repTableRow = $doctrine->getRepository(TableRow::class);
        $this->entityManager = $doctrine->getManager();
        $this->conn = $this->entityManager->getConnection();
        
    }
    public function testFindLoadingMaterials()
    {
        $tableRow = $this->repTableRow->findLoadingFieldsSeparately(83083);
        $this->assertEquals('szt',$tableRow->getMaterials()[1]->getUnit());
    }

    public function testFindLoadingMyTable(Type $var = null)
    {
        $tableRow = $this->repTableRow->findLoadingFieldsSeparately(83083);
        $this->assertEquals(5,$tableRow->getMyTable()->getMyNumber());
    }
    public function testFindLoadingChapter()
    {
        $tableRow = $this->repTableRow->findLoadingFieldsSeparately(83043);
        $chapter = $tableRow->getMyTable()->getMyChapter();
        $this->assertEquals('Rozdział 03',$chapter->getName());

    }
    public function testFindLoading_TrId()
    {
        $tableRow = $this->repTableRow->findLoadingFieldsSeparately(85012);
        $this->assertEquals(85012,$tableRow->getId());
    }
    public function testFindLoading_FullName()
    {
        $tableRow = $this->repTableRow->findLoadingFieldsSeparately(76381);
        $this->assertEquals('KNR   2-02 0612-06',$tableRow->getFullName());
    }
    public function testFindLoading_Equipments()
    {
        $tableRow = $this->repTableRow->findLoadingFieldsSeparately(76381);
        $this->assertEquals('m-g',$tableRow->getEquipments()[1]->getUnit());
    }
    public function testFindLoading_Labors()
    {
        $tableRow = $this->repTableRow->findLoadingFieldsSeparately(76321);
        $this->assertEquals(0.3341,$tableRow->getLabors()[1]->getValue());
    }
    public function testFindLoadingSeparatelyWithPrices()
    {
        $tableRow = $this->repTableRow->findLoadingSeparatelyWithPrices(75626,47);
        $this->assertGreaterThan(0,$tableRow->getMaterials()[0]->getPriceDivBy100());
    }
    public function testFindLoadingSeparatelyWithPrices_Materials()
    {
        $tableRow = $this->repTableRow->findLoadingSeparatelyWithPrices(75626,47);
        $this->assertGreaterThan(0,count($tableRow->getMaterials()));
    }
    public function testFindLoadingSeparatelyWithPrices_Equipment()
    {
        $tableRow = $this->repTableRow->findLoadingSeparatelyWithPrices(75626,47);
        $this->assertGreaterThan(0,count($tableRow->getEquipments()));
    }
}
