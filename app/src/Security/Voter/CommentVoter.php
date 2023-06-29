<?php
/**
 * Comment voter.
 */

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class CommentVoter.
 */
class CommentVoter extends Voter
{
    /**
     * View permission.
     *
     * @const string
     */
    public const VIEW = 'VIEW';

    /**
     * Delete permission.
     *
     * @const string
     */
    public const DELETE = 'DELETE';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool Result
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::DELETE])
            && $subject instanceof Comment;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute Permission name
     * @param mixed          $subject   Object
     * @param TokenInterface $token     Security token
     *
     * @return bool Vote result
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::VIEW => $this->canView($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            default => false,
        };
    }

    /**
     * Checks if user can view comment.
     *
     * @param Comment $comment Comment entity
     * @param User    $user    User
     *
     * @return bool Result
     */
    private function canView(Comment $comment, User $user): bool
    {
        $userRoles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $userRoles, true)) {
            return 1;
        }

        return $comment->getAuthor() === $user;
    }

    /**
     * Checks if user can delete comment.
     *
     * @param Comment $comment Comment entity
     * @param User    $user    User
     *
     * @return bool Result
     */
    private function canDelete(Comment $comment, User $user): bool
    {
        $userRoles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $userRoles, true)) {
            return 1;
        }

        return $comment->getAuthor() === $user;
    }
}
