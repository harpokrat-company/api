<?php

namespace App\Entity\VaultOwnership;

use App\Entity\Vault;
use Doctrine\Common\Collections\Collection;

trait VaultOwnerTrait
{
    public function getVaultOwnership(): VaultOwnership
    {
        return $this->vaultOwnership;
    }

    public function getVaults(): Collection
    {
        return $this->vaultOwnership->getVaults();
    }

    public function addVault(Vault $vault): self
    {
        if (!$this->vaultOwnership->getVaults()->contains($vault)) {
            $this->vaultOwnership[] = $vault;
            $vault->setOwner($this);
        }

        return $this;
    }

    public function removeVault(Vault $vault): self
    {
        if ($this->vaultOwnership->getVaults()->contains($vault)) {
            $this->vaultOwnership->getVaults()->removeElement($vault);
            // set the owning side to null (unless already changed)
            if ($vault->getOwner() === $this) {
                $vault->setOwner(null);
            }
        }

        return $this;
    }
}
