<?php

namespace App\Tests;

use App\Entity\Kosztorys;
use PHPUnit\Framework\TestCase;

class KosztorysTest extends TestCase
{
    public function testKonwersjaDomyslnejTabeliZRepository()
    {
        $rawTabela = [
            ['obmiar'=>10,'price_value'=>124,'value'=>0.24],
            ['obmiar'=>15,'price_value'=>135,'value'=>1.34],
            ['obmiar'=>35,'price_value'=>235,'value'=>1.33],
        ];
        $expTabela = ['obmiar'=>[10,15,35],
                    'price_value'=>[124,135,235],
                    'value'=>[0.24,1.34,1.33]
                    ];

        
        $result = Kosztorys::KonwersjaDomyslnejTabeliZRepository($rawTabela);
        // print_r($expTabela);
        // print_r($result);
        $this->assertEquals($expTabela,$result);
    }
    public function testWartoscICeneZbazyDotablicy_KluczamiPozycjaKosztId()
    {
        $rawTabela = [['pk_id'=>10,'price_value'=>124,'value'=>0.24],
        ['pk_id'=>15,'price_value'=>135,'value'=>1.34],
        ['pk_id'=>15,'price_value'=>235,'value'=>1.33]];
        $expTabela = [
            10=>[['price_value'=>124,'value'=>0.24]],
            15=>[['price_value'=>135,'value'=>1.34],
            ['price_value'=>235,'value'=>1.33]
            ]
        ];
        $result = Kosztorys::WartoscICeneZbazyDotablicy_KluczamiPozycjaKosztId($rawTabela);
        $this->assertEquals($expTabela,$result);
    }
    
    public function testZaladujSymboleIopisyPozycjiOrazWartosciIcenyDoWyliczenia_LiczbaPozycjiSymbol()
    {
        $kosztorys = new Kosztorys;
        $kosztorys->ZaladujSymboleIopisyPozycjiOrazWartosciIcenyDoWyliczenia($this->SymboleIopisy1(),$this->WartosciIceny1());

        $this->assertEquals(5,count($kosztorys->getPozycjeKosztorysowe()));
        $pozycja = $kosztorys->getPozycjeKosztorysowe()[2];
        $this->assertEquals('KNR 0-35 0121-06',$pozycja->getPodstawaNormowa()->getFullName());

        
    }
    public function testZaladujSymboleIopisyPozycjiOrazWartosciIcenyDoWyliczenia_IdPozycji()
    {
        $kosztorys = new Kosztorys;
        $kosztorys->ZaladujSymboleIopisyPozycjiOrazWartosciIcenyDoWyliczenia($this->SymboleIopisy1(),$this->WartosciIceny1());
        $this->assertEquals(3,$kosztorys->getPozycjeKosztorysowe()[2]->getId());
    }
    public function testZaladujSymboleIopisyPozycjiOrazWartosciIcenyDoWyliczenia_WartoscNakladu()
    {
        $kosztorys = new Kosztorys;
        $kosztorys->ZaladujSymboleIopisyPozycjiOrazWartosciIcenyDoWyliczenia($this->SymboleIopisy1(),$this->WartosciIceny1());
        
        $pozycja = $kosztorys->getPozycjeKosztorysowe()[2];
        $mat = $pozycja->getPodstawaNormowa()->getMaterials()[3];
        $this->assertEquals(5,$pozycja->getObmiar());
        $this->assertEquals(7,count($pozycja->getPodstawaNormowa()->getMaterials()));
        $this->assertEquals(3.15,$mat->getValue());
    }
    public function testZaladujSymboleIopisyPozycjiOrazWartosciIcenyDoWyliczenia_PrzeliczPozycje()
    {
        $kosztorys = new Kosztorys;
        $kosztorys->ZaladujSymboleIopisyPozycjiOrazWartosciIcenyDoWyliczenia($this->SymboleIopisy1(),$this->WartosciIceny1());
        $pozycja = $kosztorys->getPozycjeKosztorysowe()[2];
        $this->assertEquals(10505.23,$pozycja->getCenaZnarzutami());
    }
    

    private function SymboleIopisy1()
    {
        return [
            ['pk_id'=>1,'myNumber'=>3,'unit'=>'szt.','subDescription'=>'117$1$^$ typu S 190 o poj. 186 dm3$^$$ - 03','mainDescription'=>'168$0.3$Gazowe pojemnościowe podgrzewacze wody użytkowej, stojące$$ wraz z podejściem$$19','ct_myNumber'=>19,'cp_name'=>'Rozdział 01','cat_name'=>'KNR 0-35','obmiar'=>12],
            ['pk_id'=>2,'myNumber'=>6,'unit'=>'kpl.','subDescription'=>'127$1$^$, montowane przy pomocy rur i kształtek;$ poj. do 120 dm3$$ - 06','mainDescription'=>'177$0.10$Zasobnikowe podgrzewacze wody użytkowej, stojące, współpracujące z kotłami grzewczymi$$$$21','ct_myNumber'=>21,'cp_name'=>'Rozdział 01','cat_name'=>'KNR 0-35','obmiar'=>5],
            ['pk_id'=>3,'myNumber'=>6,'unit'=>'kpl.','subDescription'=>'127$1$^$, montowane przy pomocy rur i kształtek;$ poj. do 120 dm3$$ - 06','mainDescription'=>'177$0.10$Zasobnikowe podgrzewacze wody użytkowej, stojące, współpracujące z kotłami grzewczymi$$$$21','ct_myNumber'=>21,'cp_name'=>'Rozdział 01','cat_name'=>'KNR 0-35','obmiar'=>5],
            ['pk_id'=>4,'myNumber'=>6,'unit'=>'m2','subDescription'=>'26$1$^$15% $^$ na ścianach$ - 06','mainDescription'=>'35$0.6$(z.III) malowanie zwykłe farbą wapienną z dodatkiem $$farby emulsyjnej tynków wewnętrznych$$20','ct_myNumber'=>20,'cp_name'=>'Rozdział 15','cat_name'=>'NNRnkbORGBUD 202','obmiar'=>25],
            ['pk_id'=>5,'myNumber'=>2,'unit'=>'m2','subDescription'=>'54$1$^pilśniowych miękkich $na lepiku $^ w pomieszczeniach o pow. do 8 m2 $- dwie warstwy$-02.02','mainDescription'=>'71$0.16$Izolacja z płyt $$pod posadzki$$06','ct_myNumber'=>6,'cp_name'=>'Dział 07','cat_name'=>'KNP cz.02','obmiar'=>25.3]
        ];
    }
    private function WartosciIceny1()
    {
        return [
            ['pk_id'=>1,'r'=>'m','unit'=>'szt','price_value'=>23090,'value'=>1],
            ['pk_id'=>1,'r'=>'m','unit'=>'szt','price_value'=>3372,'value'=>4.2],
            ['pk_id'=>1,'r'=>'m','unit'=>'szt','price_value'=>22148,'value'=>3.15],
            ['pk_id'=>1,'r'=>'m','unit'=>'szt','price_value'=>12696,'value'=>2.1],
            ['pk_id'=>1,'r'=>'m','unit'=>'szt','price_value'=>5873,'value'=>6.3],
            ['pk_id'=>1,'r'=>'m','unit'=>'szt','price_value'=>8732,'value'=>1.05],
            ['pk_id'=>1,'r'=>'m','unit'=>'szt','price_value'=>11616,'value'=>3.15],
            ['pk_id'=>1,'r'=>'m','unit'=>'m','price_value'=>28133,'value'=>0.9],
            ['pk_id'=>1,'r'=>'m','unit'=>'szt','price_value'=>14588,'value'=>1],
            ['pk_id'=>1,'r'=>'m','unit'=>'szt','price_value'=>26355,'value'=>1],
            ['pk_id'=>2,'r'=>'m','unit'=>'szt','price_value'=>26912,'value'=>1],
            ['pk_id'=>2,'r'=>'m','unit'=>'szt','price_value'=>22148,'value'=>3.15],
            ['pk_id'=>2,'r'=>'m','unit'=>'szt','price_value'=>28332,'value'=>2.1],
            ['pk_id'=>2,'r'=>'m','unit'=>'szt','price_value'=>5873,'value'=>3.15],
            ['pk_id'=>2,'r'=>'m','unit'=>'szt','price_value'=>678,'value'=>2.1],
            ['pk_id'=>2,'r'=>'m','unit'=>'szt','price_value'=>11616,'value'=>2.1],
            ['pk_id'=>2,'r'=>'m','unit'=>'szt','price_value'=>5793,'value'=>1.05],
            ['pk_id'=>3,'r'=>'m','unit'=>'szt','price_value'=>26912,'value'=>1],
            ['pk_id'=>3,'r'=>'m','unit'=>'szt','price_value'=>22148,'value'=>3.15],
            ['pk_id'=>3,'r'=>'m','unit'=>'szt','price_value'=>28332,'value'=>2.1],
            ['pk_id'=>3,'r'=>'m','unit'=>'szt','price_value'=>5873,'value'=>3.15],
            ['pk_id'=>3,'r'=>'m','unit'=>'%','price_value'=>678,'value'=>2.1],//zmiana na procenty
            ['pk_id'=>3,'r'=>'m','unit'=>'szt','price_value'=>11616,'value'=>2.1],
            ['pk_id'=>3,'r'=>'m','unit'=>'szt','price_value'=>5793,'value'=>1.05],
            ['pk_id'=>4,'r'=>'m','unit'=>'kg','price_value'=>16290,'value'=>0.04],
            ['pk_id'=>4,'r'=>'m','unit'=>'kg','price_value'=>19789,'value'=>0.006],
            ['pk_id'=>4,'r'=>'m','unit'=>'m3','price_value'=>5694,'value'=>0.0003],
            ['pk_id'=>4,'r'=>'m','unit'=>'kg','price_value'=>13705,'value'=>0.004],
            ['pk_id'=>4,'r'=>'m','unit'=>'dm3','price_value'=>20472,'value'=>0.0434],
            ['pk_id'=>4,'r'=>'m','unit'=>'kg','price_value'=>7123,'value'=>0.0585],
            ['pk_id'=>1,'r'=>'e','unit'=>'m-g','price_value'=>7164,'value'=>0.21],
            ['pk_id'=>2,'r'=>'e','unit'=>'m-g','price_value'=>7164,'value'=>0.09],
            ['pk_id'=>3,'r'=>'e','unit'=>'m-g','price_value'=>7164,'value'=>0.09],
            ['pk_id'=>4,'r'=>'e','unit'=>'m-g','price_value'=>13403,'value'=>0.0005]
        ];
    }
    
}
