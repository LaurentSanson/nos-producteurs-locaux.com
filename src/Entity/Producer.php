<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Producer
 * @package App\Entity
 * @ORM\Entity
 */
class Producer extends User
{
    public const ROLE = "producer";

    /**
     * @ORM\OneToOne(targetEntity=Farm::class, mappedBy="producer", cascade={"persist", "remove"})
     */
    private $farm;

    public function getRoles(): array
    {
        return ['ROLE_PRODUCER'];
    }

    public function getFarm(): ?Farm
    {
        return $this->farm;
    }

    public function setFarm(Farm $farm): self
    {
        // set the owning side of the relation if necessary
        if ($farm->getProducer() !== $this) {
            $farm->setProducer($this);
        }

        $this->farm = $farm;

        return $this;
    }
}
