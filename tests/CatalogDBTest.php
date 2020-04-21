<?php

namespace App\Tests;

use App\Entity\Catalog;
use App\Entity\Chapter;
use App\Entity\TableRow;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class CatalogDBTest extends KernelTestCase
{
    private $entityManager;
    private $repCatalog;
    private $repChapter;
    private $repTableRow;
    
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $doctrine = $this->entityManager = $kernel->getContainer()
        ->get('doctrine');

        $this->entityManager = $doctrine->getManager();
        $this->repCatalog = $doctrine->getRepository(Catalog::class);
        $this->repChapter = $doctrine->getRepository(Chapter::class);
        $this->repTableRow = $doctrine->getRepository(TableRow::class);
    }
    public function testCountChaptersPersistedCatalog()
    {
        $this->entityManager->getConnection()->beginTransaction();
        $catFile = 'resources/Norma3/Kat/2-28/';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile,TABLE);//,CHAPTER
        $this->entityManager->persist($catalog);
        $this->entityManager->flush();
        $foundCatalog = $this->repCatalog->findOneBy(array('name'=>'KNR   2-28'));
        $this->entityManager->getConnection()->rollBack();
        $this->assertEquals(7,count($foundCatalog->getMyChapters()));
    }
    public function testPersistCatalogTableLevel()
    {
        $this->entityManager->getConnection()->beginTransaction();
        $catFile = 'resources/Norma3/Kat/2W18/';
        $catalog = new Catalog;
        // $catalog->ReadFromDir($catFile,CHAPTER);//,CHAPTER
        $catalog->ReadFromDir($catFile,TABLE);//,CHAPTER
        $this->entityManager->persist($catalog);
        $this->entityManager->flush();
        $foundCatalog = $this->repCatalog->findOneBy(array('name'=>'KNR(W) 2-18'));
        $this->entityManager->getConnection()->rollBack();
        $this->assertEquals(10,count($foundCatalog->getMyChapters()));
    }
    public function testTableMainDescriptionAfterCatalogPersist()
    {
        $this->entityManager->getConnection()->beginTransaction();
        $catFile = 'resources/Norma3/Kat/2W18/';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile,TABLE_ROW);//TABLE
        $this->entityManager->persist($catalog);
        $this->entityManager->flush();
        $chapter = $this->repChapter->findOneBy(array('name'=>'Rozdział 07'));
        $this->entityManager->getConnection()->rollBack();
        $expected = '56$0.15$próba wodna szczelności sieci wodociągowych z rur typu HOBAS, PCW, PVC, PE, PEHD o śr.nominalnej $[..]$ mm$$04';
        $this->assertEquals($expected,trim($chapter->getTables()[3]->getMainDescription()));

    }
    public function testCirculationsValueFromPersistedCatalog(Type $var = null)
    {
        $this->entityManager->getConnection()->beginTransaction();
        $catFile = 'resources/Norma3/Kat/0-39/';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile,DESCRIPaRMS|BAZ_FILE_DIST);//TABLE
        $this->entityManager->persist($catalog);
        $this->entityManager->flush();
        $chapter = $this->repChapter->findOneBy(array('name'=>'Rozdział 01'));
        $this->entityManager->getConnection()->rollBack();
        // $this->assertTrue($chapter == null);
        $tableRow17_2 = $chapter->getTables()[17]->getTableRows()[2];
        $equipment = $tableRow17_2->getEquipments()[0];
        $this->assertEquals(0.0024,$equipment->getValue());
    }
    //poniższy test powinien działać ale wykonanie zajmuje około minuty
    public function _testTableRowRepFindByDescrFragment(Type $var = null)
    {
        $this->entityManager->getConnection()->beginTransaction();
        $catFile = 'resources/Norma3/Kat/2-02/';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile,DESCRIPaRMS);//TABLE
        $this->entityManager->persist($catalog);
        $this->entityManager->flush();
        $tabRows = $this->repTableRow->findByDescriptionFragment('odgromników');
        $this->entityManager->getConnection()->rollBack();
        $this->assertEquals(4,count($tabRows));
    }
    public function testRemovePersistedChapter()
    {
        $this->entityManager->getConnection()->beginTransaction();
        $catFile = 'resources/Norma3/Kat/0-15/';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile,DESCRIPaRMS|BAZ_FILE_DIST);//TABLE
        $this->entityManager->persist($catalog);
        $this->entityManager->flush();
        // $tabRows = $this->repTableRow->findByDescriptionFragment('odgromników');
        $this->entityManager->remove($catalog);
        $this->entityManager->flush();
        $chapter = $this->repChapter->findOneBy(array('name'=>'Rozdział 05'));
        $this->entityManager->getConnection()->rollBack();
        $this->assertEquals(null,$chapter);
    }
    public function _testPersistWithoutRollback()
    {
        // $this->entityManager->getConnection()->beginTransaction();
        $catFile = 'resources/Norma3/Kat/0-12/';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile,DESCRIPaRMS);//TABLE
        // $this->entityManager->persist($catalog);
        // $this->entityManager->flush();
        // $tabRows = $this->repTableRow->findByDescriptionFragment('odgromników');
        $catToRemove = $this->repCatalog->findOneBy(array('name' => 'KNR   0-12'));
        $this->entityManager->remove($catToRemove);
        // $this->entityManager->flush();
        // $chapter = $this->repChapter->findOneBy(array('name'=>'Rozdział 05'));
        // $this->entityManager->getConnection()->rollBack();
        // $this->assertEquals(null,$chapter);
    }
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; 
    }
}
