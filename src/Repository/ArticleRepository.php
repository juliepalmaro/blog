<?php

namespace App\Repository;

use App\Entity\Article;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findOneArticle(?string $id): Article
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

    public function findAllArticles(?int $start, ?int $length, ?string $orderBy, ?string $order, ?int $userId): array
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

    public function setDefaultValues(Article $article): Article
    {
        if (!$article->getState()) {
            $article->setState('validated');
        }

        if (!$article->getCreationDate()) {
            $article->setCreationDate(new DateTime());
        }

        if (!$article->getPublic()) {
            $article->setPublic(true);
        }

        return $article;
    }
}
