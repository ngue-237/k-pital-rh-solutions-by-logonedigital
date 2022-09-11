<?php

namespace App\Repository;

use App\Entity\CategoryJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategoryJob>
 *
 * @method CategoryJob|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryJob|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryJob[]    findAll()
 * @method CategoryJob[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryJob::class);
    }

    public function add(CategoryJob $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CategoryJob $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

       /**
    * @return CategoryJob[] Returns an array of CategoryJob objects
    */
   public function listCategories(): array
   {
       return $this->createQueryBuilder('c')
           ->orderBy('c.createdAt', 'DESC')
           ->setMaxResults(6)
           ->getQuery()
           ->getResult()
       ;
   }

      /**
    * @return CategoryJob[] Returns an array of CategoryJob objects
    */
   public function listAllCategoriesJobByDate(): array
   {
       return $this->createQueryBuilder('c')
           ->orderBy('c.createdAt', 'DESC')
           ->addOrderBy('c.designation', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }
      /**
    * @return CategoryJob[] Returns an array of CategoryJob objects
    */
   public function categoryJobNavbar(): array
   {
       return $this->createQueryBuilder('c')
           ->orderBy('c.createdAt', 'DESC')
           ->addOrderBy('c.designation', 'ASC')
           ->setMaxResults(3)
           ->getQuery()
           ->getResult()
       ;
   }

   /**
    * @return CategoryJob[] an array of Job objects
    */
   public function searchCategory($value): array
   {
       return $this->createQueryBuilder('c')
           ->where('c.designation LIKE :val')
           ->setParameter('val', '%'.$value.'%')
           ->orderBy('c.createdAt', 'DESC')
           ->getQuery()
           ->getResult()
       ;
   }

     /**
    * @return CategoryJob[] an array of Job objects
    */
   public function listJobsByCategory($value): array
   {
       return $this->createQueryBuilder('c')
           ->andWhere('c.designation = :val')
           ->setParameter('val', $value)
           ->orderBy('c.createdAt', 'DESC')
           ->getQuery()
           ->getResult()
       ;
   }

//    /**
//     * @return CategoryJob[] Returns an array of CategoryJob objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CategoryJob
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
