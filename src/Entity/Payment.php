<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // L'utilisateur qui paie
    #[ORM\ManyToOne(targetEntity: "App\Entity\User")]
    private ?User $user = null;

    // L'événement concerné
    #[ORM\ManyToOne(targetEntity: "App\Entity\Evenement")]
    private ?Evenement $evenement = null;

    // Identifiant de la session Stripe (pour retrouver la transaction)
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeSessionId = null;

    // Ex: pending, paid, canceled, refunded...
    #[ORM\Column(length: 50)]
    private ?string $status = null;

    // Date de création du paiement
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Montant du paiement (en euros, par ex.)
    #[ORM\Column(nullable: true)]
    private ?float $amount = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->status = 'pending'; 
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): self
    {
        $this->evenement = $evenement;
        return $this;
    }

    public function getStripeSessionId(): ?string
    {
        return $this->stripeSessionId;
    }

    public function setStripeSessionId(?string $stripeSessionId): self
    {
        $this->stripeSessionId = $stripeSessionId;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }
}

