<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\KosztorysRepository")
 */
class Kosztorys
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PriceList")
     */
    private $poczatkowaListaCen;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PozycjaKosztorysowa", mappedBy="kosztorys", orphanRemoval=true)
     */
    private $pozycjeKosztorysowe;

    public function __construct()
    {
        $this->pozycjeKosztorysowe = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getPoczatkowaListaCen(): ?PriceList
    {
        return $this->poczatkowaListaCen;
    }

    public function setPoczatkowaListaCen(?PriceList $poczatkowaListaCen): self
    {
        $this->poczatkowaListaCen = $poczatkowaListaCen;

        return $this;
    }

    /**
     * @return Collection|PozycjaKosztorysowa[]
     */
    public function getPozycjeKosztorysowe(): Collection
    {
        return $this->pozycjeKosztorysowe;
    }

    public function addPozycjeKosztorysowe(PozycjaKosztorysowa $pozycjeKosztorysowe): self
    {
        if (!$this->pozycjeKosztorysowe->contains($pozycjeKosztorysowe)) {
            $this->pozycjeKosztorysowe[] = $pozycjeKosztorysowe;
            $pozycjeKosztorysowe->setKosztorys($this);
        }

        return $this;
    }

    public function removePozycjeKosztorysowe(PozycjaKosztorysowa $pozycjeKosztorysowe): self
    {
        if ($this->pozycjeKosztorysowe->contains($pozycjeKosztorysowe)) {
            $this->pozycjeKosztorysowe->removeElement($pozycjeKosztorysowe);
            // set the owning side to null (unless already changed)
            if ($pozycjeKosztorysowe->getKosztorys() === $this) {
                $pozycjeKosztorysowe->setKosztorys(null);
            }
        }

        return $this;
    }
}
