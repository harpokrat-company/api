<?php


namespace App\Entity;


interface EncryptionKeyInterface
{
    public function getEncryptionKey(): ?Secret;

    public function setEncryptionKey(?Secret $encryptionKey): self;
}