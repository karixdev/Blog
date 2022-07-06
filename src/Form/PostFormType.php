<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('banner', FileType::class, [
                'label' => 'Banner image',
                'mapped' => false,
                'required' => $options['is_image_required'],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'File must be format of jpg/jpeg/png',
                    ])
                ],
            ])
            ->add($options['submit_btn_text'], SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary float-end',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'csrf_protection' => true,

            'is_image_required' => true,

            'submit_btn_text' => 'add',
            'attr' => [
                'class' => 'post-form',
            ],
        ]);
    }
}
