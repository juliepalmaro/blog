<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\Response;

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

    public function findOne(?string $id): Article
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

    public function findAllArticles(?int $start, ?int $length, ?string $orderBy, ?string $order, ?int $userId, ?bool $onlyBookmarked, ?bool $onlyShared): array
    {
        !$order ?  $order = 'DESC' : $order = $order;
        !$orderBy ?  $orderby = 'a.creationDate' : $orderby = $orderBy;

        $query = $this->createQueryBuilder('a');

        if ($userId) {
            $query
                ->andWhere('a.userId = :val')
                ->setParameter('val', $userId);
        }

        !$length ?  $length = 20 : $length = $length;

        return $query
            ->orderBy($orderby, $order)
            ->setMaxResults($length)
            ->getQuery()
            ->getResult();
    }

    public function createOrUpdateArticle(?Article $article): Article
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($article);
        $entityManager->flush();

        $id = $article->getId();

        return $this->findOne($id);
    }

    public function delete(?Article $article): bool
    {
        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->delete($article);
            $entityManager->flush();
        } catch (Exception $err) {
            return false;
        }

        return true;
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
