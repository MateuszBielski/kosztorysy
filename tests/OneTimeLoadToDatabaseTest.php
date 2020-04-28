<?php

namespace App\Tests;

use App\Entity\Catalog;
use App\Service\PersistanceOptimizer;
use App\Service\BuildUniqueCirculations;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class OneTimeLoadToDatabaseTest extends KernelTestCase
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
        // $this->repCatalog = $doctrine->getRepository(Catalog::class);
        // $this->repChapter = $doctrine->getRepository(Chapter::class);
        // $this->repTableRow = $doctrine->getRepository(TableRow::class);
    }
    
    public function _testOptimizerCreateFile()
    {
        $catFileNames = array('resources/Norma3/Kat/KNZ-14/',
                                'resources/Norma3/Kat/S-215/',
                                );
        $catalogs = array ();

        // $uniqueCirculations = null;
        for($i = 0 ; $i < 2 ; $i++)
        {
            $catalog = new Catalog;
            $catalog->ReadFromDir($catFileNames[$i],DESCRIPaRMS|BAZ_FILE_DIST);
            $catalogs[] = $catalog;
        }
           
        $this->po->Aggregate($catalogs);
        $this->po->GenerateSqlFile('load');
        $fileSql = @fopen('load.sql','r');
        $result = $fileSql != false;
        $this->assertTrue($result);
    }
    public function testOptimizerCreateSql()
    {
        $commonDir = 'resources/Norma3/Kat/';
        $catalogs = Catalog::LoadFrom($commonDir,DESCRIPaRMS|BAZ_FILE_DIST);

        
        $uc = new BuildUniqueCirculations($this->entityManager);
        $uc->AddCirculationsFromCatalogCollection($catalogs);
        
        // $this->conn->beginTransaction();
        $uc->persistUniqueCirculations();

        $this->po->Aggregate($catalogs);
        $this->po->GenerateSqlFile('loadAll');
        $fileSql = @fopen('loadAll.sql','r');
        $result = $fileSql != false;
        $this->assertTrue($result);
    }
}
