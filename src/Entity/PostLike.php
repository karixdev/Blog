<?php

namespace App\Entity;

use App\Repository\PostLikeRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostLikeRepository::class)]
class PostLike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $likedAt;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    private Post $post;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLikedAt(): ?DateTimeInterface
    {
        return $this->likedAt;
    }

    public function setLikedAt(DateTimeInterface $likedAt): self
    {
        $this->likedAt = $likedAt;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
