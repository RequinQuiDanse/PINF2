<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $priceMin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $priceMax = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $pricingUnit = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $pricingDetails = null;

    #[ORM\Column]
    private ?bool $isActive = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPriceMin(): ?string
    {
        return $this->priceMin;
    }

    public function setPriceMin(?string $priceMin): static
    {
        $this->priceMin = $priceMin;

        return $this;
    }

    public function getPriceMax(): ?string
    {
        return $this->priceMax;
    }

    public function setPriceMax(?string $priceMax): static
    {
        $this->priceMax = $priceMax;

        return $this;
    }

    public function getPricingUnit(): ?string
    {
        return $this->pricingUnit;
    }

    public function setPricingUnit(?string $pricingUnit): static
    {
        $this->pricingUnit = $pricingUnit;

        return $this;
    }

    public function getPricingDetails(): ?string
    {
        return $this->pricingDetails;
    }

    public function setPricingDetails(?string $pricingDetails): static
    {
        $this->pricingDetails = $pricingDetails;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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

    public function getPriceRange(): string
    {
        if ($this->priceMin && $this->priceMax) {
            return sprintf('De %s € à %s €', number_format((float)$this->priceMin, 2, ',', ' '), number_format((float)$this->priceMax, 2, ',', ' '));
        } elseif ($this->priceMin) {
            return sprintf('À partir de %s €', number_format((float)$this->priceMin, 2, ',', ' '));
        } elseif ($this->priceMax) {
            return sprintf('Jusqu\'à %s €', number_format((float)$this->priceMax, 2, ',', ' '));
        }
        return 'Sur devis';
    }
}
