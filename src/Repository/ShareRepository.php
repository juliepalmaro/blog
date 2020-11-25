<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Share;
use App\Entity\User;
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

    public function findOneShared(User $user, Article $article)
    {
        $query = $this->createQueryBuilder('a');

        if ($user && $article) {
            $query
                ->andWhere('a.user = :user')
                ->setParameter('user', $user)
                ->andWhere('a.article = :article')
                ->setParameter('article', $article);
        }

        return $query
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllShared(?int $start, ?int $length, ?string $orderBy, ?string $order, ?User $user, ?Article $article): array
    {
        !$order ?  $order = 'DESC' : $order = $order;
        !$orderBy ?  $orderby = 'a.creationDate' : $orderby = 'a.' . $orderBy;

        $query = $this->createQueryBuilder('a');

        if ($user) {
            $query
                ->andWhere('a.user = :user')
                ->setParameter('user', $user);
        }

        if ($article) {
            $query
                ->andWhere('a.article = :article')
                ->setParameter('article', $article);
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
