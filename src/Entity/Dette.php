<?php

namespace App\Entity;
use App\enums\Status;

use App\Repository\DetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetteRepository::class)]
class Dette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column]
    private ?float $montantVerser = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'dettes')]   
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    /**
     * @var Collection<int, DetailDette>
     */
    #[ORM\OneToMany(mappedBy: 'dette', targetEntity: DetailDette::class, cascade: ['persist', 'remove'])]
    private Collection $detailDettes;

    // #[ORM\Column(type: 'string', enumType: Status::class)] 
    // private Status $status = Status::NONSOLDER;  


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->detailDettes = new ArrayCollection();

    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getMontantVerser(): ?float
    {
        return $this->montantVerser;
    }

    public function setMontantVerser(float $montantVerser): static
    {
        $this->montantVerser = $montantVerser;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }
    // public function getStatus(): ?Status
    // {
    //     return $this->status;
    // }

    // public function setStatus(?Status $status): static
    // {
    //     $this->status = $status ?? Status::NonSolde;
    //     return $this;
    // }
    
    // private function updateStatus(): void
    // {
    //     if ($this->montant - $this->montantVerser === 0) {
    //         $this->setStatus(Status::Solde);
    //     } else {
    //         $this->setStatus(Status::NonSolde);
    //     }
    // }

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
            $detailDette->setDette($this);
        }

        return $this;
    }

    public function removeDetailDette(DetailDette $detailDette): static
    {
        if ($this->detailDettes->removeElement($detailDette)) {
            // set the owning side to null (unless already changed)
            if ($detailDette->getDette() === $this) {
                $detailDette->setDette(null);
            }
        }

        return $this;
    }
}
