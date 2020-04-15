<?php

namespace App\Tests;

use App\Entity\Catalog;
use App\Service\PersistanceOptimizer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PersistanceOptimizerTest extends KernelTestCase
{
    private $entityManager;
    private $conn;
    private $po;
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $doctrine = $this->entityManager = $kernel->getContainer()
        ->get('doctrine');

        $this->entityManager = $doctrine->getManager();
        $this->conn = $this->entityManager->getConnection();
        $this->po = new PersistanceOptimizer($this->entityManager);
    }
    public function testReadAutoIncrementValues()
    {
        $this->assertTrue($this->po->RetirieveLastAutoIncFor('catalog') >= 1 );
        $this->assertTrue($this->po->RetirieveLastAutoIncFor('chapter') >= 1 );
        $this->assertTrue($this->po->RetirieveLastAutoIncFor('cl_table') >= 1 );
        $this->assertTrue($this->po->RetirieveLastAutoIncFor('table_row') >= 1 );
    }
    /*
    zebrać wszystkie katalogi do jednej tablicy
    podobnie rozdziały, tablice, i nakłady
    */
    public function testCountAggregatedCatlogs(Type $var = null)
    {
        $commonDir = 'resources/Norma3/Kat/';
        $this->po->Aggregate(Catalog::LoadFrom($commonDir));
        $this->assertEquals(258,count($this->po->getCatalogs()));
    }
    public function testCountAggregatedChapters(Type $var = null)
    {
        $catFile1 = 'resources/Norma3/Kat/2-02/';
        $catFile2 = 'resources/Norma3/Kat/2-01/';

        $catalog1 = new Catalog;
        $catalog1->ReadFromDir($catFile1,TABLE_ROW);
        $catalog2 = new Catalog;
        $catalog2->ReadFromDir($catFile2,TABLE_ROW);
        $this->po->Aggregate(array($catalog1,$catalog2));
        $this->assertEquals(25+7,count($this->po->getChapters()));
        $this->assertEquals(679,count($this->po->getTables()));
        $this->assertEquals(4603,count($this->po->getTableRows()));
        
    }
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; 
    }
}
