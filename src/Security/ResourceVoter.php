<?php


namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class ResourceVoter extends Voter
{
    private $resourceClass;

    public function __construct($resourceClass)
    {
        $this->resourceClass = $resourceClass;
    }

    protected function supports($attribute, $subject)
    {
        if (!$subject instanceof $this->resourceClass) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $attributesFunctions = $this->getAttributesFunctions();
        if (!array_key_exists($attribute, $attributesFunctions)) {
            return $this->attributeDefault($attribute, $subject, $token);
        }
        return $this->getAttributesFunctions()[$attribute]($subject, $token);
    }

    protected function attributeDefault($attribute, $subject, TokenInterface $token) {
        return true;
    }

    abstract protected function getAttributesFunctions(): array;
}