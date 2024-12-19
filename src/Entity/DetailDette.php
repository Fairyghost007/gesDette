<?php

namespace App\Entity;

use App\Repository\DetailDetteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailDetteRepository::class)]
class DetailDette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'detailDettes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dette $dette = null;

    #[ORM\ManyToOne(inversedBy: 'detailDettes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column]
    private ?int $quantiteDette = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDette(): ?Dette
    {
        return $this->dette;
    }

    public function setDette(?Dette $dette): static
    {
        $this->dette = $dette;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getQuantiteDette(): ?int
    {
        return $this->quantiteDette;
    }

    public function setQuantiteDette(int $quantiteDette): static
    {
        $this->quantiteDette = $quantiteDette;

        return $this;
    }
}
