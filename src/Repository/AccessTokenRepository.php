<?php

namespace App\Repository;

use App\Entity\AccessToken;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<AccessToken>
 */
class AccessTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessToken::class);
    }

    public function findOneByValue($value): ?AccessToken
        {
            return $this->createQueryBuilder('a')
                ->andWhere('a.value = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult();
        }
}
