<?php

namespace App\Repository;

use App\Entity\Pasantia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pasantia|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pasantia|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pasantia[]    findAll()
 * @method Pasantia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasantiaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pasantia::class);
    }
    /**
     * Devuelve los pasante 
     */
    public function findByPasantePasantia($idPasantia)
    {
       
    }
    // /**
    //  * @return Pasantia[] Returns an array of Pasantia objects
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
    public function findOneBySomeField($value): ?Pasantia
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
