<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
#[ORM\Table(name: 'panier')]
class Panier
{
    // 1ère partie de la clé primaire : Le lien vers la commande
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Commande::class)]
    #[ORM\JoinColumn(name: 'ref_com', referencedColumnName: 'ref_com', nullable: false)]
    private ?Commande $commande = null;

    // 2ème partie de la clé primaire : Le lien vers le produit
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(name: 'id_produit', referencedColumnName: 'id_produit', nullable: false)]
    private ?Produit $produit = null;

    // La quantité (c'est une colonne normale)
    #[ORM\Column(nullable: true)]
    private ?int $qte = null;

    // --- GETTERS ET SETTERS ---

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;
        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;
        return $this;
    }

    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(?int $qte): static
    {
        $this->qte = $qte;
        return $this;
    }
}