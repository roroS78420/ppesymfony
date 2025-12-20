<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommandeRepository; // Assure-toi que ce repository existe ou retire cette ligne si non utilisé

#[ORM\Entity]
#[ORM\Table(name: 'commande')]
class Commande
{
    // On dit à Symfony que l'ID c'est 'ref_com'
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ref_com')]
    private ?int $id = null;

    // On lie $idUser à la colonne 'id_u'
    #[ORM\Column(name: 'id_u')]
    private ?int $idUser = null;

    #[ORM\Column(name: 'date_commande', type: 'date')]
    private ?\DateTimeInterface $dateCommande = null;

    // Ta table oblige d'avoir un total
    #[ORM\Column(name: 'total')]
    private ?float $total = null;

    // --- GETTERS & SETTERS ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): static
    {
        $this->idUser = $idUser;
        return $this;
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
}