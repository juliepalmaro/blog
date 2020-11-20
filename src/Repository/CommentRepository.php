<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findOneComment(?string $id): Comment
    {
        $query = $this->createQueryBuilder('a');

        if ($id) {
            $query
                ->andWhere('a.id = :val')
                ->setParameter('val', $id);
        }

        return $query
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllComments(?int $start, ?int $length, ?string $orderBy, ?string $order, ?int $userId): array
    {
        !$order ?  $order = 'DESC' : $order = $order;
        !$orderBy ?  $orderby = 'a.creationDate' : $orderby = 'a.' . $orderBy;

        $query = $this->createQueryBuilder('a');

        if ($userId) {
            $query
                ->andWhere('a.userId = :val')
                ->setParameter('val', $userId);
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
