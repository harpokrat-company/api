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

    const MACROS = [
        'create-' => 'create',
        'edit-' => 'edit',
        'view-' => 'view',
    ];

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $attributesFunctions = $this->getAttributesFunctions();
        if (!array_key_exists($attribute, $attributesFunctions)) {
            foreach (self::MACROS as $from => $to) {
                if (substr($attribute, 0, strlen($from)) === $from) {
                    return $this->voteOnAttribute($to, $subject, $token);
                }
            }
            return $this->attributeDefault($attribute, $subject, $token);
        }
        return $attributesFunctions[$attribute]($subject, $token);
    }

    protected function attributeDefault($attribute, $subject, TokenInterface $token) {
        return true;
    }

    abstract protected function getAttributesFunctions(): array;
}