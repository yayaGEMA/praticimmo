<?php

namespace App\Repository;

use App\Entity\Logement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Logement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Logement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Logement[]    findAll()
 * @method Logement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Logement::class);
    }

    /**
     * Méthode retournant les x derniers logements.
     */
    public function findLasts(int $number) : array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.publicationDate', 'DESC')
            ->setMaxResults($number)
            ->getQuery()
            ->getResult()
            ;
    }
}
