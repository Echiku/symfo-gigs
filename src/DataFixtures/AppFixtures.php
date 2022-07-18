<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Listings;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        /*
        'title' =>$this->faker->sentence(),
            'tags' =>'Laravel, api, Backend',
            'company' =>$this->faker->company(),
            'email' =>$this->faker->companyEmail(),
            'website' =>$this->faker->url(),
            'location' =>$this->faker->city(),
            'description' =>$this->faker->paragraph(5),
        */
        $user=$manager->getRepository(User::class)->find(1);

        $faker= Factory::create();

        for ($i=0; $i < 10; $i++) { 

            $listing= new Listings();



            $listing->setTitle($faker->sentence())
                    ->setTags('Symfony','api','Backend')
                    ->setCompany($faker->company())
                    ->setEmail($faker->companyEmail())
                    ->setWebsite($faker->url())
                    ->setlocation($faker->city())
                    ->setDescription($faker->paragraph(5))
                    ->setUser($user);

                    $manager->persist($listing);


        }

               $manager->flush();
    }
}
