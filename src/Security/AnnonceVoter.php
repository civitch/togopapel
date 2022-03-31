<?php


namespace App\Security;


use App\Entity\Annonce;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class AnnonceVoter extends Voter
{

    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Annonce) {
            return false;
        }
        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param mixed $subject
     *
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {

        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        if (
            $this->security->isGranted('ROLE_SUPER_ADMIN') ||
            $this->security->isGranted('ROLE_ADMIN') ||
            $this->security->isGranted('ROLE_MODERATEUR')
        ) {
            return true;
        }

        /** @var Annonce $annonce */
        $annonce = $subject;

        switch ($attribute) {
            case self::VIEW:
            case self::DELETE:
            case self::EDIT:
                return $this->isOwner($annonce, $user);
                break;
        }

        throw new \LogicException('This code should not be reached!');
    }


    private function isOwner(Annonce $annonce, User $user)
    {
        return $user === $annonce->getUser();
    }
}
