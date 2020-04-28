<?php

namespace App\Tests;

use App\Entity\Catalog;
use App\Entity\Chapter;
use App\Entity\TableRow;
use App\Service\BuildUniqueCirculations;
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
    public function _testCountAggregatedCatalogs(Type $var = null)
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
        $this->assertEquals(10707,count($this->po->getLabors()));
        $this->assertEquals(19806,count($this->po->getMaterials()));
        $this->assertEquals(7606,count($this->po->getEquipments()));
        
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
    //jest długi i robi to samo, co następny
    public function _testPersistTableRows()
    {
        $catFile1 = 'resources/Norma3/Kat/2-02/';
        $catFile2 = 'resources/Norma3/Kat/2-01/'; 

        $catalog1 = new Catalog;
        $catalog1->ReadFromDir($catFile1,DESCRIPaRMS|BAZ_FILE_DIST);//
        $catalog2 = new Catalog;
        $catalog2->ReadFromDir($catFile2,DESCRIPaRMS|BAZ_FILE_DIST);//
        $chapters = $catalog1->getMyChapters();
        $uc = new BuildUniqueCirculations($this->entityManager);
        $uc->AddCirculationsFromCatalogCollection(array($catalog1,$catalog2));
        $this->po->Aggregate(array($catalog1,$catalog2));
        $this->conn->beginTransaction();
        $uc->persistUniqueCirculations();
        $this->po->persist();
        $tabRows = $this->repTableRow->findByDescriptionFragment('odgromników');
        $this->conn->rollBack();
        $this->assertEquals(4,count($tabRows));
        
        $material = $tabRows[2]->getMaterials()[0];
        $this->assertEquals(0.3908,$tabRows[2]->getTotalLaborValue());
        $this->assertEquals(0.097,$material->getValue());
    }
    public function testRetrieveCirculationNamesFromDB(Type $var = null)
    {
        $catFileNames = array('resources/Norma3/Kat/KNZ-14/',
                                'resources/Norma3/Kat/S-215/',
                                'resources/Norma3/Kat/2-02/');
        $catalogs = array ();

        // $uniqueCirculations = null;
        for($i = 0 ; $i < 3 ; $i++)
        {
            $catalog = new Catalog;
            $catalog->ReadFromDir($catFileNames[$i],DESCRIPaRMS|BAZ_FILE_DIST);
            $catalogs[] = $catalog;
        }
            // foreach($catalogs[2]->getMyChapters() as $k => $chap)
            // {
            //     echo "\n".$k." ".$chap->getName();
            // }
        $this->po->Aggregate($catalogs);

        $uc = new BuildUniqueCirculations($this->entityManager);
        $uc->AddCirculationsFromCatalogCollection($catalogs);
        
        $this->conn->beginTransaction();
        $uc->persistUniqueCirculations();
        $this->po->persist();
        $tr = $this->repTableRow->findByDescriptionFragment('gęstożebrowy')[2];
        $trOdgrom = $this->repTableRow->findByDescriptionFragment('odgromników')[2];
        $cat2_02 = $this->repCatalog->findOneBy(array('name' => 'KNR   2-02'));
        $this->conn->rollBack();
        
        $this->assertEquals(1.4187,$tr->getTotalLaborValue());
        $this->assertEquals(8.3,$tr->getMaterials()[0]->getValue());
        $this->assertEquals('beton B-15',$tr->getMaterials()[2]->getName());
        $this->assertEquals(0.3908,$trOdgrom->getTotalLaborValue());
        $this->assertEquals(0.097,$trOdgrom->getMaterials()[0]->getValue());
        //testGroupNumber
        $tables = $cat2_02->getMyChapters()[1]->getTables();
        $trCheckGroupNumber = $tables[36]->getTableRows()[0];
        $equipToCheck = $trCheckGroupNumber->getEquipments()[9];
        
        $this->assertEquals(6,$equipToCheck->getGroupNumber());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; 
    }
}
