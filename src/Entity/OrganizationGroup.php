<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrganizationGroupRepository")
 */
class OrganizationGroup
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
     * @var string
     * @ORM\Column(type="string", length=180)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var Organization
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization", inversedBy="groups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organization;

    /**
     * @var array
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="organizations")
     */
    private $members;

    /**
     * @var array
     * @ORM\OneToMany(targetEntity="App\Entity\OrganizationGroup", mappedBy="parent")
     */
    private $children;

    /**
     * @var OrganizationGroup
     * @ORM\ManyToOne(targetEntity="App\Entity\OrganizationGroup", inversedBy="children")
     * @ORM\JoinColumn(nullable=true)
     */
    private $parent;

    /**
     * OrganizationGroup constructor.
     */
    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function setOrganization(Organization $organization): self
    {
        $this->organization = $organization;
        return $this;
    }

    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function setMembers(array $members): self
    {
        $this->members = $members;
        return $this;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
        }
        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function setChildren($children): self
    {
        $this->children = $children;
        return $this;
    }

    public function addChild(OrganizationGroup $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
        }
        return $this;
    }

    public function removeChild(OrganizationGroup $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
        }
        return $this;
    }

    /**
     * @return OrganizationGroup|null
     */
    public function getParent(): ?OrganizationGroup
    {
        return $this->parent;
    }

    /**
     * @param OrganizationGroup|null $parent
     */
    public function setParent(?OrganizationGroup $parent): void
    {
        $this->parent = $parent;
    }
}