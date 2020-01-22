<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Thread;
use App\Entity\PostVote;
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

        // CREATION DES UTILISATEURS 
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
        // FIN DE CREATION DES UTILISATEURS

        /********** CREATION DE THREADS  ***********/
        for ($i = 0; $i <= 2; $i++) {
            $thread = new Thread();

            $thread->setTitle($faker->sentence());


            /****** CREATION DE POSTS *************/
            for ($j = 0; $j <= mt_rand(0, 15); $j++) {
                $user = $users[mt_rand(0, count($users) - 1)];
                $content = '<p>' . join('</p><p>', $faker->paragraphs(mt_rand(1, 2))) . '</p>';

                $post = new Post();
                $post->setAuthor($user)
                    ->setThread($thread)
                    ->setContent($content);
                $manager->persist($post);

                for ($k = 0; $k < mt_rand(0, 10); $k++) {
                    $like = new PostVote();
                    $like->setPost($post)
                        ->setUser($faker->randomElement($users));
                    $manager->persist($like);
                }
            }
            $manager->persist($thread);
        }

        $manager->flush();
    }
}
