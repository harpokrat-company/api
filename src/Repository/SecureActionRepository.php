<?php

namespace App\Repository;

use App\Entity\SecureAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SecureAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method SecureAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method SecureAction[]    findAll()
 * @method SecureAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SecureActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SecureAction::class);
    }

    // /**
    //  * @return SecureAction[] Returns an array of SecureAction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SecureAction
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
