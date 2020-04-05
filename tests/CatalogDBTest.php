<?php

namespace App\Tests;

use App\Entity\Catalog;
use App\Entity\Chapter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class CatalogDBTest extends KernelTestCase
{
    private $entityManager;
    private $repCatalog;
    private $repChapter;
    
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $doctrine = $this->entityManager = $kernel->getContainer()
        ->get('doctrine');

        $this->entityManager = $doctrine->getManager();
        $this->repCatalog = $doctrine->getRepository(Catalog::class);
        $this->repChapter = $doctrine->getRepository(Chapter::class);
    }
    public function testCountChaptersPersistedCatalog()
    {
        $this->entityManager->getConnection()->beginTransaction();
        $catFile = 'resources/Norma3/Kat/2-28/';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile);//,CHAPTER
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
    public function testCheckTableMainDescriptionAfterCatalogPersist()
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
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; 
    }
}
