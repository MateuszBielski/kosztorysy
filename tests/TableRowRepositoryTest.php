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
        $tableRow = $this->repTableRow->findLoadingMaterials(83083);
        $this->assertEquals('szt',$tableRow->getMaterials()[1]->getUnit());
    }
}
