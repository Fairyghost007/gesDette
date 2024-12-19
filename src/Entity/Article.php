<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column]
    private ?int $qteStock = null;

    /**
     * @var Collection<int, DetailDette>
     */
    #[ORM\OneToMany(targetEntity: DetailDette::class, mappedBy: 'article')]
    private Collection $detailDettes;

    public function __construct()
    {
        $this->detailDettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

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

    public function getQteStock(): ?int
    {
        return $this->qteStock;
    }

    public function setQteStock(int $qteStock): static
    {
        $this->qteStock = $qteStock;

        return $this;
    }

    /**
     * @return Collection<int, DetailDette>
     */
    public function getDetailDettes(): Collection
    {
        return $this->detailDettes;
    }

    public function addDetailDette(DetailDette $detailDette): static
    {
        if (!$this->detailDettes->contains($detailDette)) {
            $this->detailDettes->add($detailDette);
            $detailDette->setArticle($this);
        }

        return $this;
    }

    public function removeDetailDette(DetailDette $detailDette): static
    {
        if ($this->detailDettes->removeElement($detailDette)) {
            // set the owning side to null (unless already changed)
            if ($detailDette->getArticle() === $this) {
                $detailDette->setArticle(null);
            }
        }

        return $this;
    }
}
