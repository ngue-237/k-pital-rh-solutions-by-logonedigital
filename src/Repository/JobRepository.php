<?php

namespace App\Repository;

use App\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Job>
 *
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    public function add(Job $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Job $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

       /**
    * @return Job[] Returns an array of Job objects
    */
   public function recentJob(): array
   {
       return $this->createQueryBuilder('j')
           ->orderBy('j.createdAt', 'DESC')
           ->setMaxResults(3)
           ->getQuery()
           ->getResult()
       ;
   }
       /**
    * @return Job[] Returns an array of Job objects
    */
   public function listJobsHome($value): array
   {
       return $this->createQueryBuilder('j')
           ->setParameter('val', $value)
            ->where('j.expiredAt > :val')
            ->orWhere('j.expiredAt IS NULL')
            ->orderBy('j.createdAt', 'ASC')
            ->setMaxResults(6)
           ->getQuery()
           ->setCacheMode(\Doctrine\ORM\Cache::MODE_GET)
           ->setCacheable(true)
           ->setLifetime(86400)
           ->getResult()
       ;
   }

      /**
    * @return Job[] Returns an array of Job objects
    */
   public function listJobsByCategory($value): array
   {
       return $this->createQueryBuilder('j')
           ->andWhere('j.categoryJob = :val')
           ->setParameter('val', $value)
           ->orderBy('j.createdAt', 'DESC')
           ->getQuery()
           ->setCacheMode(\Doctrine\ORM\Cache::MODE_GET)
           ->setCacheable(true)
           ->setLifetime(86400)
           ->getResult()
       ;
   }


    /**
    * @return Job[] Returns an array of Job objects
    */
   public function listAllJobs($value, $filters = null, $adresseFilter=null): array
   {

        $query = $this->createQueryBuilder('j')
            ->setParameter('val', $value)
            ->where('j.expiredAt > :val')
            ->orWhere('j.expiredAt IS NULL')
            ->orderBy('j.createdAt', 'ASC');

        if($filters!=null){
            $query->andWhere('j.categoryJob IN (:cats)')
            ->setParameter(':cats', array_values($filters));
        }else if($adresseFilter!=null){
             $query->innerJoin("j.adresses", "b")
            ->andWhere('b.id = :id')
            ->setParameter('id', $adresseFilter);
        }

        else if($filters !=null and $adresseFilter !=null){
            dd("hello ");
            $query->andWhere('j.categoryJob IN (:cats)')
            ->setParameter(':cats', array_values($filters))
            ->innerJoin("j.adresses", "b")
            ->andWhere('b.id = :id')
            ->setParameter('id', $adresseFilter)
            ;
        }
        
        return $query->getQuery()
        ->setCacheMode(\Doctrine\ORM\Cache::MODE_GET)
        ->setCacheable(true)
        ->setLifetime(86400)
        ->getResult();
   }


   public function jobSearch($str, $date)
    {
        $query = $this->createQueryBuilder('j')
            ->where('j.title LIKE :titre ')
            ->setParameter('titre', '%'.$str.'%')
            ->andWhere('j.expiredAt > :date')
            ->setParameter('date',$date);

        

        return $query->getQuery()->getResult();
    }



//    /**
//     * @return Job[] Returns an array of Job objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Job
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
