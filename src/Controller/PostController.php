<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Post;

class PostController extends AbstractController
{
  /**
  * @Route("/home", name="acceuil")
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
  	$post = new Post();

  	$form = $this->createFormBuilder($post)
  		->add('Title', TextType::class, array(
        'label' => 'Titre : '
      ))
  		->add('Content', TextareaType::class, array(
        'label' => 'Article : ',
        'label_attr' => array('class' => 'foo')
      ))
  		->add('save', SubmitType::class, array('label'=>'Poster'))
  		->getForm();

  	$form->handleRequest($request);

  	if ($form->isSubmitted() && $form->isValid()){
  		$post = $form->getData();
  		$post->setDate(new \DateTime());

  		$em = $this->getDoctrine()->getManager();
  		$em->persist($post);
  		$em->flush();

  		return $this->redirectToRoute('acceuil');
  	}

  	return $this->render('add.html.twig', array(
  		'form' => $form->createView(),
  	));
  }

  /**
  * @Route("/post/{id}", name = "article")
  */
  public function viewAction($id)
  {
    $post = $this->getDoctrine()
      ->getRepository(Post::class)
      ->post($id);

    if(!$post)
    {
      throw new Exception("Cet id n'existe pas");
    }

    return $this->render('post.html.twig', array(
      'post' => $post
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