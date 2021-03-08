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

    private $cenaZnarzutami = 0.0;
    private $cenaRobociznyZnarzutami = 0.0;
    private $cenaMaterialowZnarzutami = 0.0;
    private $cenaSprzetuZnarzutami = 0.0;



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
        $this->cenaMaterialowZnarzutami = 0;
        $this->cenaRobociznyZnarzutami = 0;
        $this->cenaSprzetuZnarzutami = 0;
        foreach($materials as $mat)$this->cenaMaterialowZnarzutami += $mat->getKoszt();
        foreach($labors as $lab)$this->cenaRobociznyZnarzutami += $lab->getKoszt();
        foreach($sprzet as $e)$this->cenaSprzetuZnarzutami += $e->getKoszt();
        $this->cenaZnarzutami = $this->cenaMaterialowZnarzutami + $this->cenaRobociznyZnarzutami + $this->cenaSprzetuZnarzutami;
        // foreach($this->podstawaNormowa->getCirculations() as $cir)$this->cenaZnarzutami += $cir->getKoszt();

    }
    public function getCenaZnarzutami()
    {
        return round($this->cenaZnarzutami,2);
    }
    public function getCenaRobociznyZnarzutami()
    {
        return round($this->cenaRobociznyZnarzutami,2);
    }
    public function getCenaMaterialowZnarzutami()
    {
        return round($this->cenaMaterialowZnarzutami,2);
    }
    public function getCenaSprzetuZnarzutami()
    {
        return round($this->cenaSprzetuZnarzutami,2);
    }
    public function setCenaZnarzutami(?float $cena)
    {
        $this->cenaZnarzutami = $cena;
    }
}
