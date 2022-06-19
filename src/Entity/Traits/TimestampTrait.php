<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\HasLifecycleCallbacks]
trait TimestampTrait
{
    #[ORM\Column('created_at', 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Serializer\Groups(['timestamps'])]
    private \DateTime $createdAt;

    #[ORM\Column('updated_at', 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'], columnDefinition: 'datetime default current_timestamp on update current_timestamp not null')]
    #[Serializer\Groups(['timestamps'])]
    private \DateTime $updatedAt;

    /**
     * Get createdAt.
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt.
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get updatedAt.
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedAt.
     */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function createTimestamps(): void
    {
        $this->setCreatedAt(new DateTime());
        $this->setUpdatedAt(new DateTime());
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->setUpdatedAt(new DateTime());
    }
}
