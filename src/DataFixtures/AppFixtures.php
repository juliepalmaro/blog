<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Bookmark;
use App\Entity\Comment;
use App\Entity\Role;
use App\Entity\Share;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr FR');

        $userRole = new Role();
        $userRole->setLabel("ROLE_USER");
        $manager->persist($userRole);

        $adminRole = new Role();
        $adminRole->setLabel("ROLE_ADMIN");
        $manager->persist($adminRole);

        $manager->flush();

        $userAdmin = new User();
        $userAdmin->setEmail('admin@admin.fr')
            ->setPassword($this->encoder->encodePassword($userAdmin, 'admin'))
            ->setFirstName('admin')
            ->setLastName('admin')
            ->addUserRole($userRole)
            ->addUserRole($adminRole);
        $manager->persist($userAdmin);

        $user = new User();
        $user->setEmail('user@user.fr')
            ->setPassword($this->encoder->encodePassword($user, 'user'))
            ->setFirstName('user')
            ->setLastName('user')
            ->addUserRole($userRole);
        $manager->persist($user);

        for ($i = 0; $i < 10; $i++) {
            $article = new Article();
            $article->setTitle('Article ' . $i);
            $article->setSubTitle('article ' . $i);
            $article->setUser($userAdmin);
            $article->setContent('Content article ' . $i);
            $article->setCreationDate(new DateTime('now + ' . $i . ' hour'));
            $article->setPublic(true);
            $article->setState('validated');
            $article->setReadingTime($i * 2);
            $article->setPicture('https://img.huffingtonpost.com/asset/5e6fecb723000050280c622e.png?ops=1200_630');
            $article->setCategory('category');

            for ($j = 0; $j < 5; $j++) {
                $comment = new Comment();
                $comment->setUser($user);
                $comment->setContent('Commentaire ' . $j);
                $comment->setState('validated');
                $comment->setPrivacy('approved');
                $comment->setCreationDate(new DateTime('now + ' . $i*$j . ' hour'));
                $comment->setArticle($article);

                $manager->persist($comment);
            }

            if ($i < 3) {
                $bookmark = new Bookmark();
                $bookmark->setCreationDate(new DateTime('now + ' . $i*2 . ' hour'));
                $bookmark->setArticle($article);
                $bookmark->setUser($user);

                $manager->persist($bookmark);
            }

            if ($i > 7) {
                $share = new Share();
                $share->setCreationDate(new DateTime('now + ' . $i*3 . ' day'));
                $share->setArticle($article);
                $share->setUser($user);

                $manager->persist($share);
            }

            $manager->persist($article);
        }
        
        for ($i = 0; $i < 10; $i++) {
            $article = new Article();
            $article->setTitle('Article user ' . $i);
            $article->setSubTitle('article user ' . $i);
            $article->setUser($user);
            $article->setContent('Content article user ' . $i);
            $article->setCreationDate(new DateTime('now + ' . $i . ' day'));
            $article->setPublic(true);
            $article->setState('validated');
            $article->setReadingTime($i * 2);
            $article->setPicture('https://img.huffingtonpost.com/asset/5e6fecb723000050280c622e.png?ops=1200_630');
            $article->setCategory('category');

            for ($j = 0; $j < 5; $j++) {
                $comment = new Comment();
                $comment->setUser($userAdmin);
                $comment->setContent('Commentaire admin ' . $j);
                $comment->setState('validated');
                $comment->setPrivacy('approved');
                $comment->setCreationDate(new DateTime('now + ' . $i*$j . ' day'));
                $comment->setArticle($article);

                $manager->persist($comment);
            }

            if ($i < 3) {
                $bookmark = new Bookmark();
                $bookmark->setCreationDate(new DateTime('now + ' . $i*2 . ' day'));
                $bookmark->setArticle($article);
                $bookmark->setUser($userAdmin);

                $manager->persist($bookmark);
            }

            if ($i > 7) {
                $share = new Share();
                $share->setCreationDate(new DateTime('now + ' . $i*3 . ' day'));
                $share->setArticle($article);
                $share->setUser($userAdmin);

                $manager->persist($share);
            }

            $manager->persist($article);
        }

        $manager->flush();
    }
}
