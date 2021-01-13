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
}
