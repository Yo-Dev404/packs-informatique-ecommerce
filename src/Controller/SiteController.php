<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Comment;
use App\Form\CommentType;
use PhpParser\Node\Expr\Cast\Object_;

class SiteController extends AbstractController
{
    /**
     * @Route("/site", name="site")
     */
    public function index(ArticleRepository $repo): Response
    {
        
        $articles = $repo->findAll();
        
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
            'articles' => $articles
        ]);
    }
    
    
    /**
    * @Route("/", name="home")
    */
    public function home(){
        return $this->render('site/home.html.twig', [
            'title' => "Bienvenue chez Packs-Informatique ",
           
        ]);
    }
    /**
    * @Route("/site/new", name="site_create")
    * @Route("/site/{id}/edit", name="site_edit")
    */
    public function form(Article $article = null, Request $request, EntityManagerInterface $manager){
       
        if(!$article){
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
                if(!$article->getId())  {
                $article->setCreatedAt(new \DateTime());
           
            
            }
           

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('site_show', ['id' => $article->getId()]);
        }

        return $this ->render('site/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId()!== null
        ]);  
        }        
        
   
    /**
     * @Route("/site/{id}", name="site_show")
     */
    public function show(Article $article, Request $request, EntityManagerInterface $manager){
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);
           
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('site_show', ['id' => $article->getId()]); 
        }


        return $this-> render('site/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }


}


