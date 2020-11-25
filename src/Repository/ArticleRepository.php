<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
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

    /**
     * @param integer|null $start index du premier result
     * @param integer|null $length combien de results
     * @param string|null $orderBy ordoné par le nom du champ
     * @param string|null $order DESC ou ASC
     * @param string|null $search mot clef
     * @param User|null $user article du user
     * @return array
     */
    public function findAllArticles(?int $start, ?int $length, ?string $orderBy, ?string $order, ?string $search, ?User $user): array
    {
        !$order ?  $order = 'DESC' : $order = $order;
        !$orderBy ?  $orderby = 'a.creationDate' : $orderby = 'a.' . $orderBy;

        $query = $this->createQueryBuilder('a');

        if ($user) {
            $query
                ->andWhere('a.user = :user')
                ->setParameter('user', $user);
        }

        if ($search) {
            $query
                ->andWhere('a.titre = :val')
                ->orWhere('a.subTitle = :val')
                ->setParameter('val', $search);
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

    public function findSpecificArticle(?string $search)
    {

        $query = $this->createQueryBuilder('a');

        $query
            ->andWhere("a.title LIKE :search")
            ->setParameter("search", '%'. $search . '%');

        return $query
            ->getQuery()
            ->getResult();

    }

    public function articleFilter(?string $filter)
    {
        $orderby = null;
        $order = null;

        if($filter == 'news'){
            $orderby = 'a.creationDate';
            $order = 'DESC';
        }elseif ($filter == 'old'){
            $orderby = 'a.creationDate';
            $order = 'ASC';
        }elseif ($filter == 'a-z'){
            $orderby = 'a.title';
            $order = 'DESC';
        }elseif ($filter == 'z-a'){
            $orderby = 'a.title';
            $order = 'ASC';
        }else{
            return $query = null;
        }

        $query = $this->createQueryBuilder('a');

        return $query
            ->orderBy($orderby, $order)
            ->getQuery()
            ->getResult();

    }
}
