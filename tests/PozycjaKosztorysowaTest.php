<?php

namespace App\Tests;

// use PHPUnit\Framework\TestCase;

use App\Entity\PozycjaKosztorysowa;
use App\Entity\TableRow;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class PozycjaKosztorysowaTest extends KernelTestCase
{
    private $entityManager;
    private $conn;
    private $repTableRow;
    protected function setUp()
    {
        /*
        $kernel = self::bootKernel();
        $doctrine = $kernel->getContainer()
        ->get('doctrine');

        $this->repTableRow = $doctrine->getRepository(TableRow::class);
        $this->entityManager = $doctrine->getManager();
        $this->conn = $this->entityManager->getConnection();
        */
    }
    
    public function testCreateDependecyForRender_obmiar()
    {
        $pozycja = new PozycjaKosztorysowa;
        $params = [
            'obmiar'=>142,
        ];
        $pozycja->CreateDependecyForRender($params);
        $this->assertEquals(142,$pozycja->getObmiar());
    }
    public function testCreateDependecyForRender_Jednostka()
    {
        $pozycja = new PozycjaKosztorysowa;
        $params = [
            'unit'=>'szt',
        ];
        $pozycja->CreateDependecyForRender($params);
        $this->assertEquals('szt',$pozycja->Jednostka());
    }
    public function testCreateDependecyForRender_getId()
    {
        $pozycja = new PozycjaKosztorysowa;
        $params = [
            'pk_id'=>11,
        ];
        $pozycja->CreateDependecyForRender($params);
        $this->assertEquals(11,$pozycja->getId());
    }
    
}
