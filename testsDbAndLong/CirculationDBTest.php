<?php

namespace App\Tests;

use App\Entity\Circulation\CirculationNameAndUnit;
use App\Entity\Circulation\Material_N_U;
use App\Service\BuildUniqueCirculations;
use App\Service\CirFunctions;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CirculationDBTest extends KernelTestCase
{
     /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    private $repM,$repRMS;
    private $circulations4_04;
    private $uniqueCirculations;

    

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $doctrine = $this->entityManager = $kernel->getContainer()
        ->get('doctrine');

        $this->entityManager = $doctrine->getManager();
        $this->repM = $doctrine->getRepository(Material_N_U::class);    
        $this->repRMS = $doctrine->getRepository(CirculationNameAndUnit::class);
        $bazFile = @fopen('resources/Norma3/Kat/4-04/4-04.BAZ','r');
        $this->circulations4_04 = CirFunctions::ReadCirculationsFromBazFile($bazFile);
        fclose($bazFile);

        $fileSign = array('2-02','2W02');
        $bazFile = array();
        $uc = new BuildUniqueCirculations;
        foreach ($fileSign as $sign) {
            $bazFile = @fopen('resources/Norma3/Kat/'.$sign.'/'.$sign.'.BAZ','r');
            $originalCirculations = CirFunctions::ReadCirculationsFromBazFile($bazFile);
            fclose($bazFile);
            $uc->AddOriginalAndChangeIds($originalCirculations);
        }
        $uc->AddOriginalAndChangeIds($this->circulations4_04);
        $this->uniqueCirculations = $uc->GetUniqueCirculations();
    }

    public function testPersistAndFindOneItemWithTransactions()
    {
        $this->entityManager->getConnection()->beginTransaction();
        $materialToPersist = $this->circulations4_04['M'][8];
        $this->entityManager->persist($materialToPersist);
        $this->entityManager->flush();
        //funkcja AddOriginalAndChangeIds w setUp zmienia id
        $foundMaterial = $this->repM->find($materialToPersist->getId());
        $this->assertEquals('gwoździe budowlane okrągłe gołe',$foundMaterial->getName());
        $this->entityManager->getConnection()->rollBack();
    }
    public function testCorrectIdsReplacement()
    {
        $this->entityManager->getConnection()->beginTransaction();
        $IdTrackedMaterial = $this->circulations4_04['M'][8]->getId();
        $this->MapOverUniqueCirculations($this->entityManager,'persist',$this->uniqueCirculations);        //unique
        $this->entityManager->flush();
        $foundMaterial = $this->repRMS->find($IdTrackedMaterial);
        $this->assertEquals('gwoździe budowlane okrągłe gołe',$foundMaterial->getName());
        $this->entityManager->getConnection()->rollBack();
    }
    public function testPersistUniqueCirculationsAsOwnMethod(Type $var = null)
    {
        $uc = new BuildUniqueCirculations($this->entityManager);
        $uc->setUniqueCirculations($this->uniqueCirculations);
        $this->entityManager->getConnection()->beginTransaction();
        $IdTrackedMaterial = $this->circulations4_04['M'][8]->getId();
        // $this->MapOverUniqueCirculations($this->entityManager,'persist',$this->uniqueCirculations);        //unique
        // $this->entityManager->flush();
        $uc->PersistUniqueCirculations();
        $foundMaterial = $this->repRMS->find($IdTrackedMaterial);
        $this->entityManager->getConnection()->rollBack();
        $this->assertEquals('gwoździe budowlane okrągłe gołe',$foundMaterial->getName());
    }
    //jeśli w bazie już będą jakieś nakłady, jak będzie przechodził proces unifikacji?

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; 
    }

    private function MapOverCirculationsRMS($object,$fun,$array)
    {
        $cats = array('R','M','S');
        foreach ($cats as $cat) {
            foreach($array[$cat] as $item)
            $object->$fun($item);
        }
    }
    private function MapOverUniqueCirculations($object,$fun,$array)
    {
        foreach ($array as $item) {
            $object->$fun($item);
        }
    }
}