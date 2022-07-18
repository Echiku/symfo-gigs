<?php

namespace App\Repository;

use App\Entity\Listings;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Listings>
 *
 * @method Listings|null find($id, $lockMode = null, $lockVersion = null)
 * @method Listings|null findOneBy(array $criteria, array $orderBy = null)
 * @method Listings[]    findAll()
 * @method Listings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Listings::class);
    }

    public function add(Listings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Listings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Listings[] Returns an array of Listings objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Listings
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


public function findAllList()
    {
        return $this->createQueryBuilder('l')
        ->orderBy('l.id', 'DESC')
        ->getQuery()
        ->getResult();
    }


public function search($value): ? Array
    {
        return $this->createQueryBuilder('l')
        ->andWhere("l.title like :query OR l.tags like :query OR l.company like :query OR l.location like :query OR l.email like :query
        OR l.description like :query OR l.website like :query")
        ->setParameter('query', "%" . $value . "%")
        ->orderBy('l.id', 'DESC')
        ->getQuery()
        ->getResult();
    }

    public function findHisListing(User $user)
    {
        return $this->createQueryBuilder('l')

        ->select('l.id, l.title')
        ->andWhere('l.user = :val')
        ->setParameter('val', $user)
        ->orderBy('l.id', 'DESC')
        ->getQuery()
        ->getResult();
    }

   
}
