<?php

namespace App\Repository;

use App\Entity\Contratloc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contratloc>
 *
 * @method Contratloc|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contratloc|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contratloc[]    findAll()
 * @method Contratloc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContratlocRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contratloc::class);
    }

    public function save(Contratloc $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contratloc $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getContratLocActif($entreprise): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.locataire', 'l')
            ->andWhere('l.entreprise = :entreprise')
            ->andWhere('c.Etat = :etat')
            ->setParameter('entreprise', $entreprise)
            ->setParameter('etat', 1)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function existeContrat($appart)
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.locataire', 'l')
            ->innerJoin('c.appart', 'a')
            ->innerJoin('a.maisson', 'm')
            ->andWhere('c.Etat = :etat')
            ->andWhere('a.id = :appart')
            ->setParameter('appart', $appart)
            ->setParameter('etat', 1)
            ->getQuery()
            ->getSingleResult();
    }




    //    /**
    //     * @return Contratloc[] Returns an array of Contratloc objects
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

    //    public function findOneBySomeField($value): ?Contratloc
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
