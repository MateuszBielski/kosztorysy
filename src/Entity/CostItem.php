<?php

namespace App\Entity;

use App\Repository\ItemPriceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CostItemRepository")
 */
class CostItem extends TableRow
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $survey;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getSurvey(): ?float
    {
        return $this->survey;
    }

    public function setSurvey(?float $survey): self
    {
        $this->survey = $survey;

        return $this;
    }

    public function Initialize(TableRow $tr)
    {
        $this->myTable = $tr->getMyTable();
        $this->myNumber = $tr->getMyNumber();
        $this->unit = $tr->getUnit();
        $this->labors = ItemPrice::FactoryFromCirculations($tr->getLabors());
        $this->materials = ItemPrice::FactoryFromCirculations($tr->getMaterials());
        $this->equipments = ItemPrice::FactoryFromCirculations($tr->getEquipments());
    }
    public function GenerateValuesForTwigCostTable()
    {
        $valuesForTwig = array();
        $fillArray = function ($groupName, $circulations) use (&$valuesForTwig) {
            $row = array();
            $row[] = '';
            $row[] = $groupName;
            $valuesForTwig[] = $row;
            foreach ($circulations as $c) {
                $row = array();
                $row[] = $c->getPriceValue();
                $row[] = $c->getValue();
                $valuesForTwig[$c->getNameAndUnit()->getId()] = $row;
            }
        };
        $fillArray('--R--', $this->labors);
        $fillArray('--M--', $this->materials);
        $fillArray('--S--', $this->equipments);
        // echo "TableRow GenerateValuesForTwigCostTable";
        return $valuesForTwig;
    }
    public function UpdatePricesFromOld(ItemPriceRepository $itemPriceRepository)
    {
        $cirIds = array();
        foreach($this->getCirculations() as $cir)
        {
            $id = $cir->getNameAndUnit()->getId();
            $cirIds[] = $id;
            // echo "\nid: $id";
        }
        $itemPrices = $itemPriceRepository->findByPriceList('ceny losowe1259',$cirIds);
        // echo "\nprint_r";
        // print_r($itemPrices);
        foreach($this->getCirculations() as $emptyPrice)
        {
            $id = $emptyPrice->getNameAndUnit()->getId();
            $price = $itemPrices[$id];
            // echo "\nid $id, price $price";
            $emptyPrice->setPriceValue($price);
        }
    }
    public function UpdatePricesFrom(ItemPriceRepository $itemPriceRepository)
    {
        $cirIds = array();
        foreach($this->getCirculations() as $cir)
        {
            $id = $cir->getNameAndUnit()->getId();
            $cirIds[] = $id;
        }
        $itemPrices = $itemPriceRepository->findByPriceListAndNameUnitIds('ceny losowe1259',$cirIds);
        foreach($this->getCirculations() as $emptyPrice)
        {
            $id = $emptyPrice->getNameAndUnit()->getId();
            $price = $itemPrices[$id];
            $emptyPrice->setPriceValue($price);
        }
    }
}
