<?php

namespace App\Repository;

use App\Entity\LogementImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LogementImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogementImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogementImage[]    findAll()
 * @method LogementImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogementImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogementImage::class);
    }

    // /**
    //  * @return LogementImage[] Returns an array of LogementImage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LogementImage
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
