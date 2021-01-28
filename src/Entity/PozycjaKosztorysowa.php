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
    public function CreateDependecyForRender($params)
    {
        $obmiar = array_key_exists('obmiar',$params) ? $params['obmiar']:null;
        if($obmiar != null)$this->obmiar = $obmiar;
        $id = array_key_exists('pk_id',$params) ? $params['pk_id']:0;
        $this->id = $id;
        $this->podstawaNormowa = new TableRow;
        $this->podstawaNormowa->CreateDependecyForRender($params);
    }
}
