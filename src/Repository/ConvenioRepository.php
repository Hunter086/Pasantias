<?php

namespace App\Repository;

use App\Entity\Convenio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Convenio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Convenio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Convenio[]    findAll()
 * @method Convenio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConvenioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Convenio::class);
    }

    // /**
    //  * @return Convenio[] Returns an array of Convenio objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Convenio
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
