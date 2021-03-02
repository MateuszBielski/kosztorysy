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
    
    public function testZaladujSymboleIopisyPozycjiOrazWartosciIcenyDoWyliczenia()
    {
        
        $kosztorys = new Kosztorys;
    }
    
}
