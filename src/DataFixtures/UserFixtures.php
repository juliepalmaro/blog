<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
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


        $user = new User();
        $user->setEmail('admin@admin.fr')
            ->setPassword($this->encoder->encodePassword($user, 'admin'))
            ->setFirstName('admin')
            ->setLastName('admin')
            ->addUserRole($userRole)
            ->addUserRole($adminRole);
        $manager->persist($user);

        $user = new User();
        $user->setEmail('user@user.fr')
            ->setPassword($this->encoder->encodePassword($user, 'user'))
            ->setFirstName('user')
            ->setLastName('user')
            ->addUserRole($userRole);
        $manager->persist($user);


        $manager->flush();
    }
}
