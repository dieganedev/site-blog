<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;


class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
      
        $faker = \Faker\Factory::create('fr_Fr');

        //Crée trois fakées
        for($i = 1; $i <= 3; $i++){

            $category = new Category();
            $category->setTitre($faker->sentence())
                     ->setDescription($faker->paragraph());

            $manager->persist($category);

            //Crée entre 3 et 6 articles
            for($j = 1; $j <= mt_rand(3, 6); $j++){
                $article = new Article();
  
                $description = '<p>'.join($faker->paragraphs(5), '</p><p>').'</p>';

                $article->setTitre($faker->sentence())
                        ->setAuteur($faker->name)
                        ->setDescription($description)
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                        ->setCategory($category);
     
                $manager->persist($article);

                //On donne des commentaires à l'article
                for($k = 1; $k <=mt_rand(3, 10); $k++){

                    $comment = new Comment();

                    $content = '<p>'.join($faker->paragraphs(2), '</p> <p>').'</p>';

                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;

                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween('-' . $days . 'days'))
                            ->setArticle($article);

                            $manager->persist($comment);


                }
     
            }

        }

       
        $manager->flush();
    }
}
