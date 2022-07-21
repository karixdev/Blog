<?php

namespace App\Service;

use App\Entity\Post;
use App\Kernel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileManager
{
    private string $targetDirectory;
    private SluggerInterface $slugger;
    private Filesystem $filesystem;
    private Kernel $kernel;
    private LoggerInterface $logger;

    public function __construct(
        string $targetDirectory,
        SluggerInterface $slugger,
        Filesystem $filesystem,
        Kernel $kernel,
        LoggerInterface $logger
    )
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
        $this->filesystem = $filesystem;
        $this->kernel = $kernel;
        $this->logger = $logger;
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

    public function remove(Post $post): void
    {
        if (!($this->kernel->isDebug() && $post->getBannerFilename() === 'banner_template.jpg')) {
            try {
                $this->filesystem->remove($this->targetDirectory . '/' .$post->getBannerFilename());
            } catch (\Exception $exception) {
                // TODO: handle this exception
            }
        }
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