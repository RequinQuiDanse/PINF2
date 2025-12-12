<?php

namespace App\Entity;

use App\Repository\ServicePricingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServicePricingRepository::class)]
class ServicePricing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $serviceName = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $unitPrice = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $dailyPricing = null; // ['1-5' => 1000, '6-10' => 900, etc.]

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $pricingUnit = null; // 'jour', 'heure', 'mission', etc.

    #[ORM\Column]
    private ?bool $isActive = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceName(): ?string
    {
        return $this->serviceName;
    }

    public function setServiceName(string $serviceName): static
    {
        $this->serviceName = $serviceName;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(?string $unitPrice): static
    {
        $this->unitPrice = $unitPrice;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getDailyPricing(): ?array
    {
        return $this->dailyPricing;
    }

    public function setDailyPricing(?array $dailyPricing): static
    {
        $this->dailyPricing = $dailyPricing;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getPricingUnit(): ?string
    {
        return $this->pricingUnit;
    }

    public function setPricingUnit(?string $pricingUnit): static
    {
        $this->pricingUnit = $pricingUnit;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
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
}
