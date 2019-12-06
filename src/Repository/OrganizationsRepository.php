<?php

namespace App\Repository;

use App\Entity\Organizations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Organizations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organizations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organizations[]    findAll()
 * @method Organizations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organizations::class);
    }

//    public function findByName($name) : array
//    {
//        $em = $this->getEntityManager();
//        $query = $em->createQuery(
//            'SELECT o.id, o.name
//            FROM App\Entity\Organizations o
//            WHERE o.name = :name'
//        )->setParameter('name', $name);
//
//        return $query->getResult();
//    }

    // /**
    //  * @return Organizations[] Returns an array of Organizations objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

/*
    public function findOneByName($value): ?Organizations
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.name = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
*/
}
