<?php


namespace App\Repository;


use App\Entity\OrganizationGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method OrganizationGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrganizationGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrganizationGroup[]    findAll()
 * @method OrganizationGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizationGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationGroup::class);
    }
}