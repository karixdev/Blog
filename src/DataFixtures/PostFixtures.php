<?php

namespace App\DataFixtures;

use App\Entity\Post;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public const NUMBER_OF_POSTS = 10;
    public const POST_REFERENCE_PREFIX = 'post-';

    private const BANNER_TEMPLATE_FILENAME = 'banner_template.jpg';

    private Generator $generator;

    public function __construct()
    {
        $this->generator = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::NUMBER_OF_POSTS; $i++) {
            $post = new Post();
            $post
                ->setTitle($this->generator->realText(20))
                ->setContent($this->generator->realText(1000))
                ->setAuthor($this->getReference(UserFixtures::ADMIN_USER_REFERENCE))
                ->setBannerFilename(self::BANNER_TEMPLATE_FILENAME)
                ->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime())
            ;

            $this->addReference(self::POST_REFERENCE_PREFIX . $i, $post);
            $manager->persist($post);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}