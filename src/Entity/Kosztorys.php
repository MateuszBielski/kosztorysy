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
     * @ORM\OneToMany(targetEntity="App\Entity\PozycjaKosztorysowa", mappedBy="kosztorys", orphanRemoval=true, fetch="LAZY")
     */
    private $pozycjeKosztorysowe;

    /**
     * @ORM\Column(type="integer")
     */
    private $roboczogodzina;

    private $cenaZnarzutami = 0.0;

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

    public function getRoboczogodzina(): ?int
    {
        return $this->roboczogodzina;
    }

    public function setRoboczogodzina(int $roboczogodzina): self
    {
        $this->roboczogodzina = $roboczogodzina;

        return $this;
    }
    public function ZaladujSymboleIopisyPozycjiOrazWartosciIcenyDoWyliczenia($symboleIopisy,$wartosciIceny)
    {
        
        $wartosciIndeksowane = [];
        
        foreach($wartosciIceny as $rek)
        {
            $pk_id = array_shift($rek);
            $rodzaj =  array_shift($rek);
            $wartosciIndeksowane[$pk_id][$rodzaj][] = $rek;
        }
        foreach($symboleIopisy as $rekord)
        {
            $indeks = $rekord['pk_id'];
            if(array_key_exists($indeks,$wartosciIndeksowane)) 
            {
                $wartosci = $wartosciIndeksowane[$indeks];
                $rekord['materials'] = @$wartosci['m'];
                $rekord['equipments'] = @$wartosci['e'];
                $rekord['labors'] = @$wartosci['l'];
                
                if($this->roboczogodzina != 0)
                {
                   $ile = count($rekord['labors']);
                    for($i = 0 ; $i < $ile; $i++)
                   {
                    $rekord['labors'][$i]['price_value'] = $this->roboczogodzina;
                   }
                }
            }
            $pozycja = new PozycjaKosztorysowa;
            $this->addPozycjeKosztorysowe($pozycja);
            $pozycja->CreateDependecyForRenderAndTest($rekord);
            $pozycja->PrzeliczDlaAktualnegoObmiaru();
        }
        
    }
    public static function KonwersjaDomyslnejTabeliZRepository($rawTable)
    {
        $res = [];
        $pola = [];
        foreach($rawTable[0] as $nazwaPola => $wartosc)$pola[]=$nazwaPola;
        foreach($rawTable as $rekord)
        {
            foreach($pola as $pole)$res[$pole][] = $rekord[$pole];
        }
        return $res;
    }
    public static function WartoscICeneZbazyDotablicy_KluczamiPozycjaKosztId($rawTable)
    {
        $res = [];
        foreach($rawTable as $rek)
        {
            $res[array_shift($rek)][] = $rek;
        }
        return $res;
    }
    public function getCenaZnarzutami()
    {
        return $this->cenaZnarzutami;
    }
}
