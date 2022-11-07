<?php

namespace App\Repository;

use App\Entity\FechaModificacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FechaModificacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method FechaModificacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method FechaModificacion[]    findAll()
 * @method FechaModificacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FechaModificacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FechaModificacion::class);
    }

    // /**
    //  * @return FechaModificacion[] Returns an array of FechaModificacion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FechaModificacion
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
