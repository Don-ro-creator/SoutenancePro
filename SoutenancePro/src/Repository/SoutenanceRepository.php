<?php

namespace App\Repository;

use App\Entity\Soutenance;
use App\Entity\Enseignant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Soutenance>
 */
class SoutenanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Soutenance::class);
    }

    public function findByDate(?\DateTimeInterface $date): array
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.date', 'ASC')
            ->addOrderBy('s.heure', 'ASC');
        if ($date) {
            $qb->andWhere('s.date = :date')->setParameter('date', $date);
        }
        return $qb->getQuery()->getResult();
    }

    public function findConflictsSalle(Soutenance $soutenance): array
    {
        if (!$soutenance->getSalle() || !$soutenance->getDate() || !$soutenance->getHeure()) {
            return [];
        }
        $qb = $this->createQueryBuilder('s')
            ->where('s.salle = :salle')
            ->andWhere('s.date = :date')
            ->andWhere('s.heure = :heure')
            ->setParameter('salle', $soutenance->getSalle())
            ->setParameter('date', $soutenance->getDate())
            ->setParameter('heure', $soutenance->getHeure());
        if ($soutenance->getId()) {
            $qb->andWhere('s.id != :id')->setParameter('id', $soutenance->getId());
        }
        return $qb->getQuery()->getResult();
    }

    public function findConflictsEnseignant(Soutenance $soutenance): array
    {
        if (!$soutenance->getDate() || !$soutenance->getHeure()) {
            return [];
        }
        $enseignants = array_filter([
            $soutenance->getPresident(),
            $soutenance->getRapporteur(),
            $soutenance->getExaminateur(),
        ]);
        if (empty($enseignants)) {
            return [];
        }
        $qb = $this->createQueryBuilder('s')
            ->where('s.date = :date')
            ->andWhere('s.heure = :heure')
            ->andWhere('s.president IN (:ens) OR s.rapporteur IN (:ens) OR s.examinateur IN (:ens)')
            ->setParameter('date', $soutenance->getDate())
            ->setParameter('heure', $soutenance->getHeure())
            ->setParameter('ens', $enseignants);
        if ($soutenance->getId()) {
            $qb->andWhere('s.id != :id')->setParameter('id', $soutenance->getId());
        }
        return $qb->getQuery()->getResult();
    }

    public function findByEnseignant(Enseignant $enseignant): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.president = :e OR s.rapporteur = :e OR s.examinateur = :e')
            ->setParameter('e', $enseignant)
            ->orderBy('s.date', 'ASC')
            ->addOrderBy('s.heure', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
