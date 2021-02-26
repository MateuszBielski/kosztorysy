<?php

namespace App\Repository;

use App\Entity\PozycjaKosztorysowa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PozycjaKosztorysowa|null find($id, $lockMode = null, $lockVersion = null)
 * @method PozycjaKosztorysowa|null findOneBy(array $criteria, array $orderBy = null)
 * @method PozycjaKosztorysowa[]    findAll()
 * @method PozycjaKosztorysowa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PozycjaKosztorysowaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PozycjaKosztorysowa::class);
    }

    // /**
    //  * @return PozycjaKosztorysowa[] Returns an array of PozycjaKosztorysowa objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PozycjaKosztorysowa
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /*
    -- obmiar,pk.podstawa_normowa_id,c.value,
Select count(ip.price_value) from pozycja_kosztorysowa pk 
join material mat on pk.podstawa_normowa_id = mat.table_row_id 
join equipment equ on pk.podstawa_normowa_id = equ.table_row_id
join labor lab on pk.podstawa_normowa_id = lab.table_row_id
join circulation c on equ.id = c.id or mat.id = c.id or lab.id = c.id
join item_price ip on c.name_and_unit_id = ip.name_and_unit_id
where pk.kosztorys_id = 1 and ip.price_list_id = 47;

działa dość szybko:
select ip.price_value,c.value from 
(
	select mat.id as cir_id, pk.kosztorys_id as koszt_id from material mat  
	join pozycja_kosztorysowa pk on pk.podstawa_normowa_id = mat.table_row_id
	union 
	select equ.id as cir_id, pk.kosztorys_id as koszt_id from equipment equ
	join pozycja_kosztorysowa pk on pk.podstawa_normowa_id = equ.table_row_id
	union 
	select lab.id as cir_id, pk.kosztorys_id as koszt_id from labor lab
	join pozycja_kosztorysowa pk on pk.podstawa_normowa_id = lab.table_row_id
) as cir
join circulation c on cir.cir_id = c.id
join item_price ip on c.name_and_unit_id = ip.name_and_unit_id
where cir.koszt_id = 1 and ip.price_list_id = 47 limit 100;

również działa:
select ip.price_value,c.value from 
(
	select mat.id as cir_id, pk.kosztorys_id as koszt_id from material mat  
	join pozycja_kosztorysowa pk on pk.podstawa_normowa_id = mat.table_row_id
	union 
	select equ.id as cir_id, pk.kosztorys_id as koszt_id from equipment equ
	join pozycja_kosztorysowa pk on pk.podstawa_normowa_id = equ.table_row_id
	union 
	select lab.id as cir_id, pk.kosztorys_id as koszt_id from labor lab
	join pozycja_kosztorysowa pk on pk.podstawa_normowa_id = lab.table_row_id
) as cir
join circulation c on cir.cir_id = c.id
join item_price ip on c.name_and_unit_id = ip.name_and_unit_id
where cir.koszt_id = 1 and ip.price_list_id = 47 limit 100;

bardzo wolno działa:
select ip.price_value,c.value from item_price ip
-- select count(ip.price_value) from item_price ip
join circulation c on c.name_and_unit_id = ip.name_and_unit_id
join 
(
	select mat.id as cir_id, mat.table_row_id as tr_id from material mat  
    union
    select equ.id as cir_id, equ.table_row_id as tr_id from equipment equ
) as cir
on cir.cir_id = c.id
join pozycja_kosztorysowa pk on pk.podstawa_normowa_id = cir.tr_id
where pk.kosztorys_id = 1 and ip.price_list_id = 47 limit 100;
    */
}
