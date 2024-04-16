<?php

namespace App\Repository;

use App\Entity\CampagneContrat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CampagneContrat>
 *
 * @method CampagneContrat|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampagneContrat|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampagneContrat[]    findAll()
 * @method CampagneContrat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampagneContratRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampagneContrat::class);
    }

    public function getAllHomeProprio($campage, $proprio)
    {
        return $this->createQueryBuilder('c')
            ->select('m.LibMaison,q.LibQuartier,v.libVille,m.Localisation,m.MntCom')
            ->innerJoin('c.campagne', 'ca')
            ->innerJoin('c.proprietaire', 'p')
            ->innerJoin('c.maison', 'm')
            ->innerJoin('m.quartier', 'q')
            ->innerJoin('q.ville', 'v')
            ->andWhere('ca.id = :campagne')
            ->andWhere('p.id = :proprio')
            ->setParameter('proprio', $proprio)
            ->setParameter('campagne', $campage)
            ->groupBy('m.id')
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return CampagneContrat[] Returns an array of CampagneContrat objects
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

    //    public function findOneBySomeField($value): ?CampagneContrat
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
