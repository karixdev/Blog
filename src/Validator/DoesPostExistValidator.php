<?php

namespace App\Validator;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DoesPostExistValidator extends ConstraintValidator
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if(!$this->postRepository->find($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
