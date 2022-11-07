<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    
    public function validarIngreso(){

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')==false) {
           
            return false;
        }
        $rol=$this->getUser()->getRoles();
        $tiposdeRol= $this->tipoderol($rol);
        if ($tiposdeRol!='ROLE_ADMIN' && $tiposdeRol!='ROLE_SUPERADMIN') {
            
            return false;
        }
        return true;
    }


    public function tipoderol($rol){
        foreach ($rol as $roles) {
            if($roles=='ROLE_SUPERADMIN'){
                return $roles;
            }
            if($roles=='ROLE_ADMIN'){
                return $roles;
            }
            if($roles=='ROLE_USER'){
                return $roles;
            }
            if($roles==null){
                return '';
            }
        }
    }


    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
