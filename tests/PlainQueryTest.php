<?php

namespace App\Tests;

// use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlainQueryTest extends KernelTestCase
{
    private $entityManager;
    private $conn;
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $doctrine = $this->entityManager = $kernel->getContainer()
        ->get('doctrine');

        $this->entityManager = $doctrine->getManager();
        $this->conn = $this->entityManager->getConnection();
        
    }
    public function testFetch()
    {
        $stmt = $this->conn->executeQuery('SHOW VARIABLES LIKE ?',array('max_allowed_packet'));
        $arrResult = $stmt->fetch();
        // foreach ($arrResult as $k => $res) {
        //     # code...
        //     echo "\nXX".$k."X".$res;
        // }
        $this->assertEquals(16777216,$arrResult['Value']);
    }
    public function _testLastInsertId()
    {
        $stmt = $this->conn->executeQuery('SELECT LAST_INSERT_ID()');
        $arrResult = $stmt->fetch();
        foreach ($arrResult as $k => $res) {
            echo "\nXX".$k."X".$res;
        }
        
    }
    public function testDataBaseName()
    {
        $this->assertEquals('db_kosztorysy_test',$this->conn->getDatabase());
    }
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; 
    }
}
