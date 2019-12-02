<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\FileUploader;
use Psr\Log\LoggerInterface;

class UploadedFilesController extends AbstractController
{
    /**
     * @Route("/doUpload", name="upload")
     */
    public function index(Request $request, string $uploadDir, FileUploader $uploader, LoggerInterface $logger)
    {
        $token = $request->request->get("token");

        if (!$this->isCsrfTokenValid('upload', $token))
        {
            $logger->info("CSRF failure");

            return new Response("Operation not allowed", Response::HTTP_BAD_REQUEST,
            ['content-type' => 'text/plain']);
        }

        $file = $request->files->get('userfile');

//        var_dump($file);

        if (empty($file))
        {
            return new Response("No file specified",
            Response::HTTP_UNPROCESSABLE_ENTITY,
                ['content-type' => 'text/plain']);
        }

        $filename = $file->getClientOriginalName();

        $ext = strtolower(strrchr($filename, "."));
        $allow = ".xml";

        if ($ext == $allow)
        {
            $uploader->upload($uploadDir, $file, $filename);

            return new Response("File uploaded", Response::HTTP_OK,
                ['content-type' => 'text/plain']);
        }
        else
        {
            return new Response("Invalid file extension", Response::HTTP_BAD_REQUEST,
                ['content-type' => 'text/plain']);
        }




    }
}
