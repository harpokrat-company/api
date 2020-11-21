<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SecureActionRepository")
 */
class SecureAction
{
    const VALIDATION_URL_BASE = 'https://www.harpokrat.com/secure-action/';
    const VALIDATION_URL_ID_PARAM = 'id';
    const VALIDATION_URL_TOKEN_PARAM = 'token';

    const ACTION_VALIDATE_EMAIL_ADDRESS = 0;
    const ACTION_RESET_PASSWORD = 1;
    const ACTION_MFA = 2;

    const ACTION_IDENTIFIER = [
        self::ACTION_VALIDATE_EMAIL_ADDRESS => 'validate_email_address',
        self::ACTION_RESET_PASSWORD => 'reset_password',
        self::ACTION_MFA => 'mfa',
    ];

    const USER_CREATED_ACTIONS = [
        self::ACTION_RESET_PASSWORD,
    ];

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
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expirationDate;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $token;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validated;

    /**
     * @ORM\Column(type="json")
     */
    private $action = [];

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    public function getId()
    {
        return $this->id;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }

    public function getAction(): ?array
    {
        return $this->action;
    }

    public function setAction(array $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @return SecureAction
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getValidationLink()
    {
        $link = SecureAction::VALIDATION_URL_BASE;
        $link .= '?'.SecureAction::VALIDATION_URL_ID_PARAM.'='.$this->getId()->toString();
        $link .= '&'.SecureAction::VALIDATION_URL_TOKEN_PARAM.'='.$this->getToken();

        return $link;
    }

    /**
     * @return string
     */
    public function getActionType()
    {
        return self::ACTION_IDENTIFIER[$this->type];
    }

    public function setActionType(string $actionType)
    {
        foreach (SecureAction::ACTION_IDENTIFIER as $type => $identifier) {
            if ($actionType === $identifier) {
                $this->setType($type);

                return;
            }
        }
    }
}
