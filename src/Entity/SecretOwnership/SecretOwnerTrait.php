<?php

namespace App\Entity\SecretOwnership;

use App\Entity\Secret;
use Doctrine\Common\Collections\Collection;

trait SecretOwnerTrait
{
    public function getSecretOwnership(): SecretOwnership
    {
        return $this->secretOwnership;
    }

    public function getSecrets(): Collection
    {
        return $this->secretOwnership->getSecrets();
    }

    public function addSecret(Secret $secret): self
    {
        if (!$this->secretOwnership->getSecrets()->contains($secret)) {
            $this->secretOwnership[] = $secret;
            $secret->setOwner($this);
        }

        return $this;
    }

    public function removeSecret(Secret $secret): self
    {
        if ($this->secretOwnership->getSecrets()->contains($secret)) {
            $this->secretOwnership->getSecrets()->removeElement($secret);
            // set the owning side to null (unless already changed)
            if ($secret->getOwner() === $this) {
                $secret->setOwner(null);
            }
        }

        return $this;
    }
}
