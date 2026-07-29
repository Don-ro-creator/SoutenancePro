<?php

namespace App\Entity;

use App\Repository\EnseignantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EnseignantRepository::class)]
class Enseignant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $prenom = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    private ?string $specialite = null;

    #[ORM\OneToOne(inversedBy: 'enseignant', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'president', targetEntity: Soutenance::class)]
    private Collection $soutenancesAsPresident;

    #[ORM\OneToMany(mappedBy: 'rapporteur', targetEntity: Soutenance::class)]
    private Collection $soutenancesAsRapporteur;

    #[ORM\OneToMany(mappedBy: 'examinateur', targetEntity: Soutenance::class)]
    private Collection $soutenancesAsExaminateur;

    public function __construct()
    {
        $this->soutenancesAsPresident = new ArrayCollection();
        $this->soutenancesAsRapporteur = new ArrayCollection();
        $this->soutenancesAsExaminateur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): static
    {
        $this->specialite = $specialite;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getSoutenancesAsPresident(): Collection
    {
        return $this->soutenancesAsPresident;
    }

    public function getSoutenancesAsRapporteur(): Collection
    {
        return $this->soutenancesAsRapporteur;
    }

    public function getSoutenancesAsExaminateur(): Collection
    {
        return $this->soutenancesAsExaminateur;
    }

    public function getAllSoutenances(): array
    {
        return array_merge(
            $this->soutenancesAsPresident->toArray(),
            $this->soutenancesAsRapporteur->toArray(),
            $this->soutenancesAsExaminateur->toArray()
        );
    }

    public function __toString(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
