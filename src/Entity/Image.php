<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\Table(name: 'image')]
#[ApiResource]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_image')]
    private ?int $id = null;

    #[ORM\Column(name: 'nom_image', length: 128)]
    private ?string $nomImage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomImage(): ?string
    {
        return $this->nomImage;
    }

    public function setNomImage(string $nomImage): static
    {
        $this->nomImage = $nomImage;

        return $this;
    }
}