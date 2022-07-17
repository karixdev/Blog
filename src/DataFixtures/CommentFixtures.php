<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $generator;

    public function __construct()
    {
        $this->generator = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        // Each user adds one comment to each post
        for ($i = 0; $i < PostFixtures::NUMBER_OF_POSTS; $i++) {
            for ($j = 0; $j < UserFixtures::NUMBER_OF_NORMAL_USERS; $j++) {
                $comment = new Comment();
                $comment
                    ->setContent($this->generator->realText(300))
                    ->setAuthor($this->getReference(UserFixtures::NORMAL_USER_REFERENCE_PREFIX . $j))
                    ->setPost($this->getReference(PostFixtures::POST_REFERENCE_PREFIX . $i))
                ;

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            PostFixtures::class,
        ];
    }
}