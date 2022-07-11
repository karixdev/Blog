<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class LikeManager
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function action(object $object, User $user): void
    {
        if (!($object instanceof Post || $object instanceof Comment)) {
            throw new InvalidArgumentException();
        }

        if ($object->getLikes()->contains($user)) {
            $object->removeLike($user);
        } else {
            $object->addLike($user);
        }

        $this->entityManager->flush();
    }
}