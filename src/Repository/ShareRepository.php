<?php

namespace App\Repository;

use App\Entity\Share;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Share|null find($id, $lockMode = null, $lockVersion = null)
 * @method Share|null findOneBy(array $criteria, array $orderBy = null)
 * @method Share[]    findAll()
 * @method Share[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Share::class);
    }

    public function findOneShared(int $userId, int $articleId): Share
    {
        $query = $this->createQueryBuilder('a');

        if ($userId && $articleId) {
            $query
                ->andWhere('a.userId = :userId')
                ->setParameter('userId', $userId)
                ->andWhere('a.articleId = :articleId')
                ->setParameter('articleId', $articleId);
        }

        return $query
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllShared(?int $start, ?int $length, ?string $orderBy, ?string $order, ?int $userId, ?int $articleId): array
    {
        !$order ?  $order = 'DESC' : $order = $order;
        !$orderBy ?  $orderby = 'a.creationDate' : $orderby = 'a.' . $orderBy;

        $query = $this->createQueryBuilder('a');

        if ($userId) {
            $query
                ->andWhere('a.userId = :userId')
                ->setParameter('val', $userId);
        }

        if ($articleId) {
            $query
                ->andWhere('a.articleId = :articleId')
                ->setParameter('val', $articleId);
        }

        !$length ?  $length = 20 : $length = $length;

        return $query
            ->setFirstResult($start)
            ->orderBy($orderby, $order)
            ->setMaxResults($length)
            ->getQuery()
            ->getResult();
    }
}
