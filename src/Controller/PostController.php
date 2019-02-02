<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;

class PostController extends AbstractController
{
  /**
  * @Route("/", name="acceuil")
  */
  public function homeAction()
  {
    $listLastPosts = $this->getDoctrine()
    	->getRepository(Post::class)
    	->findLastThree();

    if (!$listLastPosts) {
    	throw new Exception("Problème de chargement des données");
    }
    return $this->render('home.html.twig', array(
      'listLastPosts' => $listLastPosts
     ));
  }

  /**
  * @Route("/add", name="ajout")
  */
  public function addAction(Request $request)
  {
  
    if( in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) 
    {
      
  	 $post = new Post();

  	 $form = $this->createFormBuilder()
  		  ->add('Title', TextType::class)
  		  ->add('Content', TextareaType::class)
        ->add('Image', FileType::class)
  		  ->add('Save', SubmitType::class, array('label'=>'Poster'))
  		  ->getForm();

  	 $form->handleRequest($request);

  	 if ($form->isSubmitted() && $form->isValid()){

        /***Gestion des entrées***/
  		  $data = $form->getData();
        $post->setTitle($data['Title']);
        $post->setContent($data['Content']);
  		  $post->setDate(new \DateTime());
        $image = $data['Image'];
        $post->setImage('images/'.$image->getClientOriginalName());
        $image->move('../public/images', $post->getImage());
        /*************/

  		  $em = $this->getDoctrine()->getManager();
  		  $em->persist($post);
  		  $em->flush();

  		  return $this->redirectToRoute('acceuil');
  	 }

  	 return $this->render('add.html.twig', array(
  		  'form' => $form->createView(),
  	 ));
    }
    else
    {
      $this->redirectToRoute('login');
    }
  }

  /**
  * @Route("/post/{id}", name = "article")
  */
  public function viewAction($id, Request $request)
  {
    /***Display Post***/
    
    $repo = $this->getDoctrine()->getRepository(Post::class);
    $post = new Post();
    $post = $repo->findOneBy(['id' => $id]);

    $listComments = $post->getComments();


    if(!$post)
    {
      throw new Exception("Cet id n'existe pas");
    }

    $comment = new Comment();
    /***Display Form***/
    $form = $this->createFormBuilder()
      ->add('Auteur', TextType::class)
      ->add('Commentaire', TextareaType::class)
      ->add('Valider', SubmitType::class)
      ->getForm();

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid())
    {
      /***Set Comment***/
      $data = $form->getData();
      $comment->setAuthor($data['Auteur']);
      $comment->setCommentaire($data['Commentaire']);
      $comment->setDate(new \DateTime());
      $comment->setPost($post);

      $em = $this->getDoctrine()->getManager();
      $em->persist($comment);
      $em->flush();

      return $this->render('post.html.twig', array(
      'title' => $post->getTitle(),
      'content' => $post->getContent(),
      'date' => $post->getDate(),
      'image' => $post->getImage(),
      'listComments' => $listComments,
      'com' => $form->createView()
    ));
    }  

    return $this->render('post.html.twig', array(
      'title' => $post->getTitle(),
      'content' => $post->getContent(),
      'date' => $post->getDate(),
      'image' => $post->getImage(),
      'listComments' =>$listComments,
      'com' => $form->createView()
    ));
  }

  /**
  * @Route("/index", name="posts")
  */
  public function indexAction()
  {
    $listPosts = $this->getDoctrine()
      ->getRepository(Post::class)
      ->listPosts();

    if(!$listPosts)
    {
      throw new Exception("erreur lors de la collecte des articles");
    }

    return $this->render('index.html.twig', array(
      'listPosts' => $listPosts
    ));
  }


}