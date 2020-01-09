<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Thread;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');

        // CREATION DES UTILISATEURS 
        $users = [];
        $genres = ['male','female'];

        for($i=1; $i <=10; $i++){
            $user = new User();
            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            if($genre == "male") $picture = $picture . 'men/' . $pictureId;
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
        for ($i=0 ; $i<= 2; $i++){
            $thread = new Thread();

            $thread->setTitle($faker->sentence());


            /****** CREATION DE POSTS *************/
            for ($j=0; $j<= mt_rand(0, 15); $j++){
                $user=$users[mt_rand(0,count($users)-1)];
                $content = '<p>' . join('</p><p>',$faker->paragraphs(mt_rand(1,2))) . '</p>';

                $post = new Post();
                $post->setAuthor($user)
                     ->setThread($thread)
                     ->setContent($content);
                $manager->persist($post);
            }
            $manager->persist($thread);
        }

        $manager->flush();
    }
}
