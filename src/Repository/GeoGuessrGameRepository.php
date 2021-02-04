<?php

namespace App\Repository;

use App\Entity\GeoGuessrGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GeoGuessrGame|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeoGuessrGame|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeoGuessrGame[]    findAll()
 * @method GeoGuessrGame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeoGuessrGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeoGuessrGame::class);
    }

    // /**
    //  * @return GeoGuessrGame[] Returns an array of GeoGuessrGame objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GeoGuessrGame
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
