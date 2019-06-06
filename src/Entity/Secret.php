<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SecretRepository")
 */
class Secret
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Accessor(getter="getId")
     */
    private $id;

    /**
     * @ORM\Column(type="blob")
     * @Serializer\Accessor(getter="getContentAsString")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="secrets")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Accessor(getter="getOwner")
     */
    private $owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getContentAsString()
    {
        $content = $this->getContent();
        if (is_string($content))
            return $content;
        return stream_get_contents($content);
    }

    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?UserInterface $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
