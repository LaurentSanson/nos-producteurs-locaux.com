<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Order
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="orders")
 */
class Order
{

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface $id;

    /**
     * @ORM\Column
     */
    private string $state = "created";

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $canceledAt = null;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $refusedAt = null;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $acceptedAt = null;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $settledAt = null;


    /**
     * @ORM\ManyToOne(targetEntity="Customer")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private Customer $customer;

    /**
     * @ORM\OneToMany(targetEntity="OrderLine", mappedBy="order", cascade="persist")
     */
    private Collection $lines;

    /**
     * @ORM\ManyToOne(targetEntity="Farm")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private Farm $farm;

    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->lines = new ArrayCollection();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable $createdAt
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param DateTimeImmutable|null $refusedAt
     */
    public function setRefusedAt(?DateTimeImmutable $refusedAt): void
    {
        $this->refusedAt = $refusedAt;
    }

    /**
     * @param DateTimeImmutable|null $acceptedAt
     */
    public function setAcceptedAt(?DateTimeImmutable $acceptedAt): void
    {
        $this->acceptedAt = $acceptedAt;
    }

    /**
     * @param DateTimeImmutable|null $settledAt
     */
    public function setSettledAt(?DateTimeImmutable $settledAt): void
    {
        $this->settledAt = $settledAt;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @return int
     */
    public function getNumberOfProducts(): int
    {
        return array_sum($this->lines->map(fn(OrderLine $line) => $line->getQuantity())->toArray());
    }

    /**
     * @return int
     */
    public function getTotalInculdingTaxes(): int
    {
        return array_sum($this->lines->map(fn(OrderLine $line) => $line->getPriceIncludingTaxes())->toArray());
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCanceledAt(): ?DateTimeImmutable
    {
        return $this->canceledAt;
    }

    /**
     * @param DateTimeImmutable|null $canceledAt
     */
    public function setCanceledAt(?DateTimeImmutable $canceledAt): void
    {
        $this->canceledAt = $canceledAt;
    }

    /**
     * @return Farm
     */
    public function getFarm(): Farm
    {
        return $this->farm;
    }

    /**
     * @param Farm $farm
     */
    public function setFarm(Farm $farm): void
    {
        $this->farm = $farm;
    }
}
