<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PozycjaKosztorysowaRepository")
 */
class PozycjaKosztorysowa
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Kosztorys", inversedBy="pozycjeKosztorysowe")
     * @ORM\JoinColumn(nullable=false)
     */
    private $kosztorys;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TableRow")
     * @ORM\JoinColumn(nullable=false)
     */
    private $podstawaNormowa;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $obmiar = 1;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKosztorys(): ?Kosztorys
    {
        return $this->kosztorys;
    }

    public function setKosztorys(?Kosztorys $kosztorys): self
    {
        $this->kosztorys = $kosztorys;
        return $this;
    }

    public function getPodstawaNormowa(): ?TableRow
    {
        return $this->podstawaNormowa;
    }

    public function setPodstawaNormowa(?TableRow $podstawaNormowa): self
    {
        $this->podstawaNormowa = $podstawaNormowa;
        if($this->kosztorys != null){
            $roboczogodzina = $this->kosztorys->getRoboczogodzina();
            foreach($podstawaNormowa->getLabors() as $lab)$lab->setPrice($roboczogodzina);
        }
        return $this;
    }

    public function getObmiar(): ?float
    {
        return $this->obmiar;
    }

    public function setObmiar(?float $obmiar): self
    {
        $this->obmiar = $obmiar;

        return $this;
    }

    public function Jednostka()
    {
        return $this->podstawaNormowa->getUnit();
    }
    public function OznaczeniePelne()
    {
        return $this->podstawaNormowa->getFullName();
    }
    public function CreateDependecyForRenderAndTest($params)
    {
        $obmiar = array_key_exists('obmiar',$params) ? $params['obmiar']:null;
        if($obmiar != null)$this->obmiar = $obmiar;
        $id = array_key_exists('pk_id',$params) ? $params['pk_id']:0;
        $this->id = $id;
        $this->podstawaNormowa = new TableRow;
        $this->podstawaNormowa->CreateDependecyForRenderAndTest($params);
    }
    public function ZmienObmiarIprzelicz(float $nowyObmiar)
    {
        $this->obmiar = $nowyObmiar;
        $this->PrzeliczDlaAktualnegoObmiaru();
    }
    public function PrzeliczDlaAktualnegoObmiaru()
    {
        foreach($this->podstawaNormowa->getCirculations() as $cir)
        {
            $cir->obliczKosztDlaObmiaru($this->obmiar);
            $cir->ObliczNakladDlaObmiaru($this->obmiar);
            $cir->ObliczKosztJednostkowy();
        }
        $labors = $this->podstawaNormowa->getLabors();
        $materials = $this->podstawaNormowa->getMaterials();
        $sprzet = $this->podstawaNormowa->getEquipments();
        $matKoszt = 0;
        $robKoszt = 0;
        $sprzKoszt = 0;
        $matProcentowe = [];
        $robProcentowe = [];
        $sprzProcentowe = [];
        foreach($materials as $mat)
        {
            if($mat->getUnit() != '%')$matKoszt += $mat->getKoszt();
            else $matProcentowe[] = $mat;
        }
        foreach($labors as $lab)
        {
            if($lab->getUnit() != '%')$robKoszt += $lab->getKoszt();
            else $robProcentowe[] = $lab;
        }
        foreach($sprzet as $e)
        {
            if($e->getUnit() != '%')$sprzKoszt += $e->getKoszt();
            else $sprzProcentowe[] = $e;
        }
        if (!$matKoszt) $matKoszt = $robKoszt + $sprzKoszt;
        if (!$robKoszt)$robKoszt = $matKoszt + $sprzKoszt;
        if (!$sprzKoszt)$sprzKoszt = $robKoszt + $matKoszt;
        foreach($matProcentowe as $mProc)$mProc->setKoszt($mProc->getValue() * $matKoszt / 100);
        foreach($sprzProcentowe as $eProc)$eProc->setKoszt($eProc->getValue() * $sprzKoszt / 100);
        foreach($robProcentowe as $rProc)$rProc->setKoszt($rProc->getValue() * $robKoszt / 100);

    }
}
