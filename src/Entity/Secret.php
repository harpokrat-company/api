<?php

namespace App\Entity;

use App\Entity\SecretOwnership\SecretOwnerInterface;
use App\Entity\SecretOwnership\SecretOwnership;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SecretRepository")
 */
class Secret
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @var SecretOwnership
     * @ORM\ManyToOne(targetEntity="App\Entity\SecretOwnership\SecretOwnership", inversedBy="secrets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $private = true;

    public function getId()
    {
        return $this->id;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getOwner(): ?SecretOwnerInterface
    {
        return $this->owner->getOwner();
    }

    public function setOwner(?SecretOwnerInterface $owner): self
    {
        $this->owner = $owner->getSecretOwnership();

        return $this;
    }

    public function isPrivate(): bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): void
    {
        $this->private = $private;
    }
}
