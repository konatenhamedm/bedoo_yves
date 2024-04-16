<?php

namespace App\Repository;

use App\Entity\CompteLocataire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CompteLocataire>
 *
 * @method CompteLocataire|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompteLocataire|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompteLocataire[]    findAll()
 * @method CompteLocataire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompteLocataireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompteLocataire::class);
    }

//    /**
//     * @return CompteLocataire[] Returns an array of CompteLocataire objects
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

//    public function findOneBySomeField($value): ?CompteLocataire
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
