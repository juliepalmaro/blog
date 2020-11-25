<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
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

    public function findAllComments(?int $start, ?int $length, ?string $orderBy, ?string $order, ?User $user, ?Article $article, ?bool $onlyValidate = true): array
    {
        !$order ?  $order = 'DESC' : $order = $order;
        !$orderBy ?  $orderby = 'a.creationDate' : $orderby = 'a.' . $orderBy;

        $query = $this->createQueryBuilder('a');

        if ($user) {
            $query
                ->andWhere('a.user = :val')
                ->setParameter('val', $user)
                ->groupBy('a.article');
        }

        if ($article) {
            $query
                ->andWhere('a.article = :article')
                ->setParameter('article', $article);
        }

        if ($onlyValidate) {
            $query
                ->andWhere('a.state = :state')
                ->setParameter('state', 'validated');
        }

        !$length ?  $length = 20 : $length = $length;

        return $query
            ->setFirstResult($start)
            ->orderBy($orderby, $order)
            ->setMaxResults($length)
            ->getQuery()
            ->getResult();
    }

    public function commentFilter(?string $filter)
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
            $orderby = 'a.content';
            $order = 'DESC';
        }elseif ($filter == 'z-a'){
            $orderby = 'a.content';
            $order = 'ASC';
        }elseif ($filter == 'authorUp'){
            $orderby = 'a.user';
            $order = 'ASC';
        }elseif ($filter == 'authorDown'){
            $orderby = 'a.user';
            $order = 'DESC';
        }else{
            return $query = null;
        }

        $query = $this->createQueryBuilder('a');

        return $query
            ->orderBy($orderby, $order)
            ->getQuery()
            ->getResult();

    }

    public function findSpecificComment(?string $search)
    {

        $query = $this->createQueryBuilder('a');

        $query
            ->andWhere("a.content LIKE :search")
            ->setParameter("search", '%'. $search . '%');

        return $query
            ->getQuery()
            ->getResult();

    }
}
