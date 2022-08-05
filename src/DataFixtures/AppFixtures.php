<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\User;
use App\Services\SlugService;
use Doctrine\DBAL\Connection;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private $slug;
    private $slugService;
    private $connection;
    
    public function __construct(SluggerInterface $slug, SlugService $slugService, Connection $connection) {
        $this->slug = $slug;
        $this->slugService = $slugService;
        $this->connection = $connection;
    }
    
    private function truncate()
    {
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0');
        $this->connection->executeQuery('TRUNCATE TABLE category');
        $this->connection->executeQuery('TRUNCATE TABLE question');
        $this->connection->executeQuery('TRUNCATE TABLE answer');
        $this->connection->executeQuery('TRUNCATE TABLE practice');
        $this->connection->executeQuery('TRUNCATE TABLE user');
        $this->connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1');
    }


    public function load(ObjectManager $manager): void
    {
        $this->truncate();


        $categoryList = [];
        $userList = [];

        // User creation
        $genre = ['homme', 'femme'];


        $userAdmin = new User();
        $userAdmin->setEmail('admin@admin.com');
        $userAdmin->setPseudo('admin');
        $userAdmin->setSlug($this->slugService->slug($userAdmin->getPseudo()));
        $userAdmin->setPassword('$2y$13$D1OpuSSUsGu1EEZdc6K7juPDc4pdQ/1CIb2/15.Wlv6KUzjakovzS');
        $userAdmin->setStatus(1);
        $userAdmin->setGenre($genre[0]);
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userList[] = $userAdmin;

        $userAnonymous = new User();
        $userAnonymous->setEmail('obaby@gmail.com');
        $userAnonymous->setPseudo('Anonymous');
        $userAnonymous->setSlug($this->slugService->slug($userAnonymous->getPseudo()));
        $userAnonymous->setPassword('$2y$13$SQCAsxHo2Pwk9vAnSpMIxuCQvBrAE.ekHEYE5eKL/ChksQASPJ1cS');
        $userAnonymous->setGenre($genre[1]);
        $userAnonymous->setStatus(1);
        $userAnonymous->setRoles(['ROLE_USER']);
        $userList[] = $userAnonymous;
        
        $manager->persist($userAdmin);

        $manager->persist($userAnonymous);

        // Category creation
        
        
        $category1 = new Category();
        $category1->setName('Grossesse');
        $categorySlug1 = $this->slug->slug($category1->getName())->lower();
        $category1->setSlug($categorySlug1);
        $manager->persist($category1);
        $categoryList[] = $category1;
        
        $category2 = new Category();
        $category2->setName('Sécurité');
        $categorySlug2 = $this->slug->slug($category2->getName())->lower();
        $category2->setSlug($categorySlug2);
        $manager->persist($category2);
        $categoryList[] = $category2;
        
        $category3 = new Category();
        $category3->setName('Nutrition');
        $categorySlug3 = $this->slug->slug($category3->getName())->lower();
        $category3->setSlug($categorySlug3);
        $manager->persist($category3);
        $categoryList[] = $category3;
        
        $category4 = new Category();
            $category4->setName('Santé et soins');
            $categorySlug4 = $this->slug->slug($category4->getName())->lower();
            $category4->setSlug($categorySlug4);
            $manager->persist($category4);
            $categoryList[] = $category4;
            
            $category5 = new Category();
            $category5->setName('Loisir');
            $categorySlug5= $this->slug->slug($category5->getName())->lower();
            $category5->setSlug($categorySlug5);
            $manager->persist($category5);
            $categoryList[] = $category5;
            
            $category6 = new Category();
            $category6->setName('Sommeil');
            $categorySlug6= $this->slug->slug($category6->getName())->lower();
            $category6->setSlug($categorySlug6);
            $manager->persist($category6);
            $categoryList[] = $category6;


        $manager->flush();
    }
}