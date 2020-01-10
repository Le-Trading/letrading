<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');

        //gestion des utilisateurs 
        $users = [];
        $genres = ['male', 'female'];

        // gestion des roles
        $adminRole = new Role();
        $superAdminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $superAdminRole->setTitle('ROLE_SUPER_ADMIN');
        $manager->persist($adminRole);
        $manager->persist($superAdminRole);

        for ($i = 1; $i <= 30; $i++) {
            $user = new User();
            $isAdmin = mt_rand(0, 1);
            if ($isAdmin)
                $user->addRole($adminRole);
            $isSuperAdmin = mt_rand(0, 10);
            if ($isSuperAdmin == 8)
                $user->addRole($superAdminRole);
            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            if ($genre == "male") $picture = $picture . 'men/' . $pictureId;
            else $picture = $picture . 'women/' . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstname($genre))
                ->setLastName($faker->lastname)
                ->setPseudo($faker->userName)
                ->setEmail($faker->email)
                ->setHash($hash)
                ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }

        $manager->flush();
    }
}
