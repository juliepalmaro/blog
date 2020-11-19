<?php

namespace App\Repository;

use App\Entity\Article;
use App\Request\GetAllArticleRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Request\GetOneArticleRequest;

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

    public function findOne(GetOneArticleRequest $request): Article
    {
        $query = $this->createQueryBuilder('a');

        if ($request->id) {
            $query
                ->andWhere('a.id = :val')
                ->setParameter('val', $request->id);
        }

        return $query
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllArticle(GetAllArticleRequest $request): array
    {
        !$request->order ?  $order = 'DESC' : $order = $request->order;
        !$request->orderby ?  $orderby = 'a.creationDate' : $orderby = $request->orderby;

        $query = $this->createQueryBuilder('a');

        if ($request->id) {
            $query
                ->andWhere('a.id = :val')
                ->setParameter('val', $request->id);
        }

        if ($request->userId) {
            $query
                ->andWhere('a.userId = :val')
                ->setParameter('val', $request->userId);
        }

        !$request->length ?  $length = 20 : $length = $request->length;

        return $query
            ->orderBy($orderby, $order)
            ->setMaxResults($length)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
