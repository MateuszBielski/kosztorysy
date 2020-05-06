<?php

namespace App\Tests;

use App\Entity\Catalog;
use App\Service\PersistanceOptimizer;
use App\Service\BuildUniqueCirculations;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

require_once('src/Service/Constants.php');

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
    /*
    mysql -u root -p

    set global net_buffer_length=1000000; --Set network buffer length to a large byte number

    set global max_allowed_packet=1000000000; --Set maximum allowed packet size to a large byte number

    SET foreign_key_checks = 0; --Disable foreign key checking to avoid delays,errors and unwanted behaviour

    source file.sql --Import your sql dump file

    SET foreign_key_checks = 1;
    
    ładowanie na dell:30 kwi 2020
    Query OK, 258 rows affected (0,55 sec)
    Records: 258  Duplicates: 0  Warnings: 0

    Query OK, 2151 rows affected (1,28 sec)
    Records: 2151  Duplicates: 0  Warnings: 0

    Query OK, 23840 rows affected (1,52 sec)
    Records: 23840  Duplicates: 0  Warnings: 0

    Query OK, 172057 rows affected (8,99 sec)
    Records: 172057  Duplicates: 0  Warnings: 0

    Query OK, 1020807 rows affected (3 min 20,44 sec)
    Records: 1020807  Duplicates: 0  Warnings: 0

    Query OK, 268810 rows affected (19,87 sec)
    Records: 268810  Duplicates: 0  Warnings: 0

    Query OK, 507788 rows affected (15,73 sec)
    Records: 507788  Duplicates: 0  Warnings: 0

    Query OK, 244209 rows affected (9,74 sec)
    Records: 244209  Duplicates: 0  Warnings: 0

    lub przy wyłączonej przeglądarce i vsc:
    Query OK, 258 rows affected (0,11 sec)
    Records: 258  Duplicates: 0  Warnings: 0

    Query OK, 2151 rows affected (0,16 sec)
    Records: 2151  Duplicates: 0  Warnings: 0

    Query OK, 23840 rows affected (1,37 sec)
    Records: 23840  Duplicates: 0  Warnings: 0

    Query OK, 172057 rows affected (6,90 sec)
    Records: 172057  Duplicates: 0  Warnings: 0

    Query OK, 1020807 rows affected (1 min 13,58 sec)
    Records: 1020807  Duplicates: 0  Warnings: 0

    Query OK, 268810 rows affected (11,79 sec)
    Records: 268810  Duplicates: 0  Warnings: 0

    Query OK, 507788 rows affected (15,61 sec)
    Records: 507788  Duplicates: 0  Warnings: 0

    Query OK, 244209 rows affected (6,19 sec)
    Records: 244209  Duplicates: 0  Warnings: 0

    Na kompie w pracy:
    Query OK, 258 rows affected (0,15 sec)
    Records: 258  Duplicates: 0  Warnings: 0

    Query OK, 2151 rows affected (0,70 sec)
    Records: 2151  Duplicates: 0  Warnings: 0

    Query OK, 23840 rows affected (2,36 sec)
    Records: 23840  Duplicates: 0  Warnings: 0

    Query OK, 172057 rows affected (5,35 sec)
    Records: 172057  Duplicates: 0  Warnings: 0

    Query OK, 1020807 rows affected (40,40 sec)
    Records: 1020807  Duplicates: 0  Warnings: 0

    Query OK, 268810 rows affected (9,00 sec)
    Records: 268810  Duplicates: 0  Warnings: 0

    Query OK, 507788 rows affected (11,23 sec)
    Records: 507788  Duplicates: 0  Warnings: 0

    Query OK, 244209 rows affected (5,99 sec)
    Records: 244209  Duplicates: 0  Warnings: 0

    */
}
