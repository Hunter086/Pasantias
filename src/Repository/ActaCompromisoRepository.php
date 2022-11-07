<?php

namespace App\Repository;

use App\Entity\ActaCompromiso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ActaCompromiso|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActaCompromiso|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActaCompromiso[]    findAll()
 * @method ActaCompromiso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActaCompromisoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActaCompromiso::class);
    }

    // /**
    //  * @return ActaCompromiso[] Returns an array of ActaCompromiso objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ActaCompromiso
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
