<?php

namespace App\Repository;

use App\Entity\Bookmark;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Bookmark|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bookmark|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bookmark[]    findAll()
 * @method Bookmark[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookmarkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bookmark::class);
    }

    public function findOneBookmark(int $userId, int $articleId): Bookmark
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

    public function findAllBookmarks(?int $start, ?int $length, ?string $orderBy, ?string $order, ?int $userId, ?int $articleId): array
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
