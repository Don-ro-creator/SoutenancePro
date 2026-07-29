<?php

namespace App\Entity;

use App\Repository\SoutenanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SoutenanceRepository::class)]
class Soutenance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'soutenance')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Etudiant $etudiant = null;

    #[ORM\ManyToOne(inversedBy: 'soutenancesAsPresident')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Enseignant $president = null;

    #[ORM\ManyToOne(inversedBy: 'soutenancesAsRapporteur')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Enseignant $rapporteur = null;

    #[ORM\ManyToOne(inversedBy: 'soutenancesAsExaminateur')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Enseignant $examinateur = null;

    #[ORM\ManyToOne(inversedBy: 'soutenances')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Salle $salle = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $heure = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): static
    {
        $this->etudiant = $etudiant;
        return $this;
    }

    public function getPresident(): ?Enseignant
    {
        return $this->president;
    }

    public function setPresident(?Enseignant $president): static
    {
        $this->president = $president;
        return $this;
    }

    public function getRapporteur(): ?Enseignant
    {
        return $this->rapporteur;
    }

    public function setRapporteur(?Enseignant $rapporteur): static
    {
        $this->rapporteur = $rapporteur;
        return $this;
    }

    public function getExaminateur(): ?Enseignant
    {
        return $this->examinateur;
    }

    public function setExaminateur(?Enseignant $examinateur): static
    {
        $this->examinateur = $examinateur;
        return $this;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): static
    {
        $this->salle = $salle;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->heure;
    }

    public function setHeure(?\DateTimeInterface $heure): static
    {
        $this->heure = $heure;
        return $this;
    }

    public function __toString(): string
    {
        return 'Soutenance de ' . ($this->etudiant ? $this->etudiant->__toString() : '');
    }
}
