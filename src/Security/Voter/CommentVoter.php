<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CommentVoter extends Voter
{
    public const EDIT = 'COMMENT_EDIT';
    public const DELETE = 'COMMENT_DELETE';

    public function __construct(
        private readonly Security $security
    )
    {
    }


    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Comment;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::DELETE => $this->canDelete($subject, $user),
            self::EDIT => $this->canEdit($subject, $user),

            default => false,
        };
    }

    public function canEdit(Comment $comment, User $user): bool
    {
        return $this->security->isGranted('ROLE_USER', $user)
                && $comment->getAuthor() === $user;
    }

    private function canDelete(Comment $comment, User $user): bool
    {
        return ($this->security->isGranted('ROLE_USER', $user) && $comment->getAuthor() === $user)
                || $this->security->isGranted('ROLE_ADMIN', $user);
    }
}
