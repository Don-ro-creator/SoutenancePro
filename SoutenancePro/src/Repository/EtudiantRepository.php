<?php

namespace App\Repository;

use App\Entity\Etudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Etudiant>
 */
class EtudiantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etudiant::class);
    }

    public function searchByNom(?string $q): array
    {
        $qb = $this->createQueryBuilder('e')
            ->orderBy('e.nom', 'ASC');
        if ($q) {
            $qb->andWhere('e.nom LIKE :q OR e.prenom LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }
        return $qb->getQuery()->getResult();
    }

    public function findWithoutSoutenance(): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.soutenance', 's')
            ->where('s.id IS NULL')
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
