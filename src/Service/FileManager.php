<?php

namespace App\Service;

use App\Entity\Post;
use App\Kernel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileManager
{

    public function __construct(
        private readonly string $targetDirectory,
        private readonly SluggerInterface $slugger,
        private readonly Filesystem $filesystem,
        private readonly Kernel $kernel,
        private readonly LoggerInterface $logger
    )
    {
    }

    public function upload(UploadedFile $file): ?string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $newFilename);
        } catch (FileException $e) {
            $this->logger->error('Error while uploading banner: ' . $e->getMessage());
            return null;
        }

        return $newFilename;
    }

    public function remove(Post $post): bool
    {
        if (!($this->kernel->isDebug() && $post->getBannerFilename() === 'banner_template.jpg')) {
            try {
                $this->filesystem->remove($this->targetDirectory . '/' .$post->getBannerFilename());
            } catch (IOException $e) {
                $this->logger->error('Error while deleting banner: ' . $e->getMessage());
                return false;
            }
        }

        return true;
    }

    public function replace(UploadedFile $file, Post $post): string
    {
        $this->remove($post);
        return $this->upload($file);
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}