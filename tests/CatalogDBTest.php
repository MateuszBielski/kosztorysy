<?php

namespace App\Tests;

use App\Entity\Catalog;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class CatalogDBTest extends KernelTestCase
{
    private $entityManager;
    private $repCatalog;
    
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $doctrine = $this->entityManager = $kernel->getContainer()
        ->get('doctrine');

        $this->entityManager = $doctrine->getManager();
        $this->repCatalog = $doctrine->getRepository(Catalog::class);
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
    public function testPersistCatalogChapterLevel()
    {
        $this->entityManager->getConnection()->beginTransaction();
        $catFile = 'resources/Norma3/Kat/2W18/';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile,CHAPTER);//,CHAPTER
        $this->entityManager->persist($catalog);
        $this->entityManager->flush();
        $foundCatalog = $this->repCatalog->findOneBy(array('name'=>'KNR(W) 2-18'));
        $this->entityManager->getConnection()->rollBack();
        $this->assertEquals(10,count($foundCatalog->getMyChapters()));
    }
    public function _testPersistChapter()
    {
        $this->entityManager->getConnection()->beginTransaction();
        $catFile = 'resources/Norma3/Kat/2W18/';
        $catalog = new Catalog;
        $catalog->ReadFromDir($catFile,CHAPTER);//,CHAPTER
        $chapter = $catalog->getMyChapters()['Rozdział 09'];
        // echo "\nXX ".$chapter->getDescription();
        $this->entityManager->persist($chapter);
        $this->entityManager->flush();
        $this->entityManager->getConnection()->rollBack();
        // $foundCatalog = $this->repCatalog->findOneBy(array('name'=>'KNR(W) 2-18'));
    }
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; 
    }
}
