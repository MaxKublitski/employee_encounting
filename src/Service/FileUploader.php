<?php


namespace App\Service;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function upload($uploadDir, $file, $filename)
    {
        try
        {
            $file->move($uploadDir, $filename);
        }
        catch (FileException $exception)
        {
            $this->logger->error('Failed to upload file: ' . $exception->getMessage());
            throw new FileException('Failed to upload file');
        }
    }
}