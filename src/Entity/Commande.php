<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\Table(name: 'commande')]
class Commande
{
    // Attention ici : on mappe l'ID sur "ref_com"
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ref_com')]
    private ?int $id = null;

    #[ORM\Column(name: 'date_commande', type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column]
    private ?float $total = null;

    // RELATION vers Users (Clé étrangère id_u)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_u', referencedColumnName: 'id', nullable: false)]
    private ?Users $users = null;

    // --- GETTERS ET SETTERS ---

    public function getId(): ?int // En PHP on l'appelle Id, mais en base ce sera ref_com
    {
        return $this->id;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): static
    {
        $this->dateCommande = $dateCommande;
        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;
        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): static
    {
        $this->users = $users;
        return $this;
    }
}