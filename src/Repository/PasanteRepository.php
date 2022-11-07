<?php

namespace App\Repository;

use App\Entity\Pasante;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pasante|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pasante|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pasante[]    findAll()
 * @method Pasante[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasanteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pasante::class);
    }
    /**
     * Devuelve los pasante 
     */
    public function findByPasantePasantia($idPasantia)
    {
        $em = $this->getEntityManager();
        $RAW_QUERY = $em->createQuery("SELECT pasante.* FROM pasante INNER JOIN pasantia_pasante ON pasantia_pasante.pasante_id = pasante.id WHERE pasante.estado_pasante='Inactivo' or pasante.estado_pasante='Activo' and pasantia_pasante.pasantia_id=1");
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();
        return $result;
    }
    // /**
    //  * @return Pasante[] Returns an array of Pasante objects
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
    public function findOneBySomeField($value): ?Pasante
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
