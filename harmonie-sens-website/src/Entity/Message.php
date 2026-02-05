<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column]
    private ?bool $isRead = false;

    #[ORM\Column]
    private ?bool $isArchived = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $repliedAt = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $requestType = null; // 'contact' ou 'appointment'

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $appointmentDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $appointmentEndDate = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $appointmentStatus = null; // 'pending', 'confirmed', 'cancelled'

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $microsoftEventId = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function isRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;
        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): static
    {
        $this->isArchived = $isArchived;
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

    public function getRepliedAt(): ?\DateTimeImmutable
    {
        return $this->repliedAt;
    }

    public function setRepliedAt(?\DateTimeImmutable $repliedAt): static
    {
        $this->repliedAt = $repliedAt;
        return $this;
    }

    public function getRequestType(): ?string
    {
        return $this->requestType;
    }

    public function setRequestType(?string $requestType): static
    {
        $this->requestType = $requestType;
        return $this;
    }

    public function getAppointmentDate(): ?\DateTimeImmutable
    {
        return $this->appointmentDate;
    }

    public function setAppointmentDate(?\DateTimeImmutable $appointmentDate): static
    {
        $this->appointmentDate = $appointmentDate;
        return $this;
    }

    public function getAppointmentEndDate(): ?\DateTimeImmutable
    {
        return $this->appointmentEndDate;
    }

    public function setAppointmentEndDate(?\DateTimeImmutable $appointmentEndDate): static
    {
        $this->appointmentEndDate = $appointmentEndDate;
        return $this;
    }

    public function getAppointmentStatus(): ?string
    {
        return $this->appointmentStatus;
    }

    public function setAppointmentStatus(?string $appointmentStatus): static
    {
        $this->appointmentStatus = $appointmentStatus;
        return $this;
    }

    public function getMicrosoftEventId(): ?string
    {
        return $this->microsoftEventId;
    }

    public function setMicrosoftEventId(?string $microsoftEventId): static
    {
        $this->microsoftEventId = $microsoftEventId;
        return $this;
    }
}
