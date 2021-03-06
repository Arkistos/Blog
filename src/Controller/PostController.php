<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Category;

class PostController extends AbstractController
{
  /**
  * @Route("/", name="accueil")
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
  * @IsGranted("ROLE_ADMIN")
  * @Route("/post/add", name="ajout")
  */
  public function addAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $listCat = $this->getDoctrine()->getRepository(Category::class)->findAll();

  	$post = new Post();

  	$form = $this->createFormBuilder()
  		->add('Title', TextType::class)
  		->add('Content', TextareaType::class)
      ->add('Image', FileType::class, ['required' => false])
      ->add('Category', ChoiceType::class, ['required' =>false, 'multiple' => true, 'expanded'=> true , 
        'choices' => $listCat,
        'choice_label' => function($category, $key, $value) {
        /** @var Category $category */
        return strtoupper($category->getName());
        },
      ])
  		->add('Save', SubmitType::class, array('label'=>'Poster'))
  		->getForm();

  	$form->handleRequest($request);

  	if ($form->isSubmitted() && $form->isValid()){

      /***Gestion des entrées***/
  		$data = $form->getData();
      $post->setTitle($data['Title']);
      $post->setContent($data['Content']);
  		$post->setDate(new \DateTime());
      $user = $this->getUser();
      $post->setAuthor($user->getName());
      foreach ($data['Category'] as $cat) {
        $cat->addPost($post);
        $em->persist($cat);
      }
      $image = $data['Image'];
      if(!is_null($image))
      {
        $post->setImage('images/'.$image->getClientOriginalName());
        $image->move('../public/images', $post->getImage());
      }
      /*************/


  		$em->persist($post);

  		$em->flush();

  		return $this->redirectToRoute('accueil');
  	 }

  	return $this->render('post/add.html.twig', array(
  		'form' => $form->createView(),
  	));
    
  }

  /**
  * @Route("/post/print/{id}", name = "article")
  */
  public function viewAction($id, Request $request)
  {
    /***Display Post***/
    
    $repo = $this->getDoctrine()->getRepository(Post::class);
    $post = new Post();
    $post = $repo->findOneBy(['id' => $id]);
    $post->setViews($post->getViews()+1);
    $this->getDoctrine()->getManager()->flush($post);
    $catgories = $post->getCategory();

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

      return $this->render('post/post.html.twig', array(

      'author' => $post->getAuthor(),
      'title' => $post->getTitle(),
      'content' => $post->getContent(),
      'date' => $post->getDate(),
      'image' => $post->getImage(),
      'views' => $post->getViews(),
      'listComments' => $listComments,
      'com' => $form->createView(),
      'listCat' => $catgories
    ));
    }  

    return $this->render('post/post.html.twig', array(
      'author' => $post->getAuthor(),
      'title' => $post->getTitle(),
      'content' => $post->getContent(),
      'date' => $post->getDate(),
      'image' => $post->getImage(),
      'views' => $post->getViews(),
      'listComments' =>$listComments,
      'com' => $form->createView(),
      'listCat' => $catgories
    ));
  }

  /**
  * @Route("post/index", name="posts")
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

    return $this->render('post/index.html.twig', array(
      'listPosts' => $listPosts
    ));
  }
}