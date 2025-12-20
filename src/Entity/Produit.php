<?php

namespace App\Entity;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\Table(name: 'produit')]
#[ApiResource]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_produit')]
    private ?int $id = null;

    #[ORM\Column(name: 'nom_produit', length: 128)]
    private ?string $nomProduit = null;

    #[ORM\Column(name: 'p_motscles', length: 280)]
    private ?string $pMotscles = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(name: 'qteProduit')]
    private ?int $qteProduit = null;

    #[ORM\Column]
    private ?float $prix = null;

    // --- RELATIONS (Clés étrangères) ---

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_categorie', referencedColumnName: 'id_categorie', nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_image', referencedColumnName: 'id_image', nullable: false)]
    private ?Image $image = null;

    // --- GETTERS ET SETTERS (Accesseurs) ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProduit(): ?string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(string $nomProduit): static
    {
        $this->nomProduit = $nomProduit;
        return $this;
    }

    public function getPMotscles(): ?string
    {
        return $this->pMotscles;
    }

    public function setPMotscles(string $pMotscles): static
    {
        $this->pMotscles = $pMotscles;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getQteProduit(): ?int
    {
        return $this->qteProduit;
    }

    public function setQteProduit(int $qteProduit): static
    {
        $this->qteProduit = $qteProduit;
        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;
        return $this;
    }
}