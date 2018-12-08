<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use \Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Comment;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'Diéblog',
        ]);
    }

     /**
     * @Route("/home", name="home")
     */
    public function home(ArticleRepository $repo)
    {
        $articles = $repo->findAll();
        return $this->render('blog/home.html.twig', [
            'articles' => $articles ,
        ]);
    }
   
    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager)
    {
        if(!$article){

            $article = new Article();
        }

         //$form = $this->createFormBuilder($article)
                    //->add('titre')
                    //->add('auteur')
                    //->add('description')
                    //->add('image')
                    //->add('catArticle')
                    //->getForm();

            $form = $this->createForm(ArticleType::class, $article);

                $form->handleRequest($request);

                if($form->isSubmitted() && $form->isValid()){

                    if(!$article->getId()){

                        $article->setCreatedAt(new \DateTime());


                    }
                    
                    $manager->persist($article);
                    $manager->flush();

                    return  $this->redirectToRoute('blog_show', [
                        'id' => $article->getId()
                    ]);

                }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/next", name="blog_type")
     */
    public function type()
    {
        return $this->render('blog/type.html.twig');
    }

     /**
     * @Route("/blog/affiche", name="blog_affiche")
     */
    public function affiche()
    {
        return $this->render('blog/affiche.html.twig');
    }
    
     /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article, Request $request, ObjectManager $manager)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);


            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/show.html.twig', [
            'article' => $article ,
            'commentForm' => $form->createView()
            ]);
    }
}
