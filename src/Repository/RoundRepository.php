<?php

namespace App\Repository;

use App\Entity\Round;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Round|null find($id, $lockMode = null, $lockVersion = null)
 * @method Round|null findOneBy(array $criteria, array $orderBy = null)
 * @method Round[]    findAll()
 * @method Round[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoundRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Round::class);
    }

    public function findByMapToken(string $mapToken)
    {
        return $this->createQueryBuilder('r')
            ->join('r.geoGuessrGame', 'g')
            ->join('g.map', 'm')
            ->where('m.token = :token')
            ->setParameter('token', $mapToken)
            ->getQuery()
            ->execute();

        /*
            SELECT * FROM geo_guessr_scores.round as r
            INNER JOIN geo_guessr_scores.geo_guessr_game as g ON r.geo_guessr_game_id = g.id
            INNER JOIN geo_guessr_scores.map as m ON g.map_id = m.id
            WHERE m.token = 'world';
        */
    }

    // /**
    //  * @return Round[] Returns an array of Round objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Round
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
