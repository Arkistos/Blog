<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, Post::class);
  }

  public function listPosts()
  {
    $qb = $this->createQueryBuilder('p');

    $query = $qb
      ->select($qb->expr()->substring('p.content' , 1, 75), 'p.id', 'p.date', 'p.title', 'p.image')
      ->orderBy('p.date', 'DESC')
      ->getQuery();

    return $query->getResult();
  }

  public function post($id)
  {
    $qb = $this->createQueryBuilder('p')
      ->andWhere('p.id = :id')
      ->setParameter('id', $id)
      ->getQuery();

    return $qb->execute();
  }

  public function findLastThree()
  {
    $qb = $this->createQueryBuilder('p');

    $query = $qb
      ->select($qb->expr()->substring('p.content', 1, 100), 'p.id', 'p.date', 'p.title', 'p.image')
      ->orderBy('p.date', 'DESC')
      ->setMaxResults(3)
      ->getQuery();
    

    return $query->getResult();
  }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
