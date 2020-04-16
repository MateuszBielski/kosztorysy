<?php

namespace App\Tests;

use App\Entity\Catalog;
use App\Entity\Chapter;
use App\Entity\TableRow;
use App\Service\PersistanceOptimizer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PersistanceOptimizerTest extends KernelTestCase
{
    private $entityManager;
    private $conn;
    private $po;
    private $repCatalog;
    private $repChapter;
    private $repTableRow;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $doctrine = $this->entityManager = $kernel->getContainer()
        ->get('doctrine');

        $this->entityManager = $doctrine->getManager();
        $this->conn = $this->entityManager->getConnection();
        $this->po = new PersistanceOptimizer($this->entityManager);
        $this->repCatalog = $doctrine->getRepository(Catalog::class);
        $this->repChapter = $doctrine->getRepository(Chapter::class);
        $this->repTableRow = $doctrine->getRepository(TableRow::class);
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
    public function _testCountAggregatedCatlogs(Type $var = null)
    {
        $commonDir = 'resources/Norma3/Kat/';
        $this->po->Aggregate(Catalog::LoadFrom($commonDir));
        $this->assertEquals(258,count($this->po->getCatalogs()));
    }
    public function testCountAggregatedChapters()
    {
        $catFile1 = 'resources/Norma3/Kat/2-02/';
        $catFile2 = 'resources/Norma3/Kat/2-01/';

        $catalog1 = new Catalog;
        $catalog1->ReadFromDir($catFile1,DESCRIPaRMS);
        $catalog2 = new Catalog;
        $catalog2->ReadFromDir($catFile2,DESCRIPaRMS);
        $this->po->Aggregate(array($catalog1,$catalog2));
        $this->assertEquals(25+7,count($this->po->getChapters()));
        $this->assertEquals(679,count($this->po->getTables()));
        $this->assertEquals(4603,count($this->po->getTableRows()));
        $this->assertEquals(11499,count($this->po->getLabors()));
        $this->assertEquals(26318,count($this->po->getMaterials()));
        $this->assertEquals(9430,count($this->po->getEquipments()));
        
    }
    public function testPersistCatalogs()
    {
        $catalog1 = new Catalog;
        $catalog1->setName('KNT 01');
        $catalog2 = new Catalog;
        $catalog2->setName('KNT 02');
        $this->po->Aggregate(array($catalog1,$catalog2));
        $this->conn->beginTransaction();
        $this->po->persist();
        $foundCatalogs = $this->repCatalog->findBy(array('name'=> array('KNT 01','KNT 02')));

        $this->conn->rollBack();
        $this->assertEquals(2,count($foundCatalogs));
        
    }
    public function testPersistChapters()
    {
        $catFile1 = 'resources/Norma3/Kat/2-02/';
        $catFile2 = 'resources/Norma3/Kat/2-01/';

        $catalog1 = new Catalog;
        $catalog1->ReadFromDir($catFile1,CHAPTER);
        $catalog2 = new Catalog;
        $catalog2->ReadFromDir($catFile2,CHAPTER);
        $this->po->Aggregate(array($catalog1,$catalog2));
        $this->conn->beginTransaction();
        $this->po->persist();
        $foundChapters = $this->repChapter->findAll();
        $this->conn->rollBack();
        $this->assertEquals(32,count($foundChapters));
    }
    public function testPersistTableRows()
    {
        $catFile1 = 'resources/Norma3/Kat/2-02/';
        $catFile2 = 'resources/Norma3/Kat/2-01/'; 
        // $catFile1 = 'resources/Norma3/Kat/0-44/';
        // $catFile2 = 'resources/Norma3/Kat/0-41/';

        $catalog1 = new Catalog;
        $catalog1->ReadFromDir($catFile1,DESCRIPaRMS);
        $catalog2 = new Catalog;
        $catalog2->ReadFromDir($catFile2,DESCRIPaRMS);
        $this->po->Aggregate(array($catalog1,$catalog2));
        // echo "\nXXX".count($this->po->getTables());
        // echo "\nXXX".count($this->po->getTableRows());
        $this->conn->beginTransaction();
        $this->po->persist();
        $tabRows = $this->repTableRow->findByDescriptionFragment('odgromników');
        $this->conn->rollBack();
        // $this->conn->commit();
        // $f = fopen('query.txt','w');
        // foreach($tabRows as $tr)
        // {
            // echo "\n".$tabRows[2]->CompoundDescription();
            // }
            // fwrite($f,$this->po->getQuery());
            // fclose($f);
        $this->assertEquals(4,count($tabRows));
        
        $material = $tabRows[2]->getMaterials()[0];
        // echo "\nXX".count($tabRows[2]->getMaterials());
        $this->assertEquals(0.3908,$tabRows[2]->getTotalLaborValue());
        $this->assertEquals(0.097,$material->getValue());
    }
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; 
    }
}
