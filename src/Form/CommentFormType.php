<?php

namespace App\Form;

use App\Entity\Comment;
use App\Validator\DoesPostExist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Range;

class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'mapped' => true,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Add comment',
                ],
            ])
            ->add('post', HiddenType::class, [
                'mapped' => false,
                'required' => true,
                'data' => $options['postId'],

                'constraints' => [
                    new DoesPostExist(),
                ],
            ])
            ->add('comment', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'csrf_protection' => true,
            'attr' => [
                'class' => 'comment-form',
            ],

            'postId' => null,
        ]);
    }
}
