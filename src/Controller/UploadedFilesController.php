<?php

namespace App\Controller;

use App\Entity\Employees;
use App\Entity\Organizations;
use App\Entity\UploadedFiles;
use App\Repository\EmployeesRepository;
use App\Repository\OrganizationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\FileUploader;
use Psr\Log\LoggerInterface;

class UploadedFilesController extends AbstractController
{
    /**
     * @var OrganizationsRepository $organizationsRepository
     */
    private $organizationsRepository;
    private $employeesRepository;

    public function __construct(OrganizationsRepository $organizationsRepository, EmployeesRepository $employeesRepository)
    {
        $this->organizationsRepository = $organizationsRepository;
        $this->employeesRepository = $employeesRepository;
    }

    /**
     * @Route("/doUpload", name="upload")
     */
    public function index(Request $request, string $uploadDir, FileUploader $uploader, LoggerInterface $logger)
    {
        $token = $request->request->get("token");

        //validating CSRF key from upload_form.html.twig
        if (!$this->isCsrfTokenValid('upload', $token))
        {
            $logger->info("CSRF failure");

            return new Response("Operation not allowed", Response::HTTP_BAD_REQUEST,
            ['content-type' => 'text/plain']);
        }

        $file = $request->files->get('userfile');

        //checking if the form is empty
        if (empty($file))
        {
            return new Response("Файл не выбран",
            Response::HTTP_UNPROCESSABLE_ENTITY,
                ['content-type' => 'text/plain']);
        }

        $fileOriginalName = $file->getClientOriginalName();
        $ext = strtolower(strrchr($fileOriginalName, ".")); //getting extension of uploaded file
        $allowedExt = ".xml";

        //validating extension of the file. if it's not .xml,
        //return "Invalid file extension" message
        if ($ext == $allowedExt)
        {
            $xml = simplexml_load_file($file);

            //TODO file structure validation
            //здесь структура файла должна проверяться на соответствие
            //со структурой таблиц базы данных. Для этого я хотел преобразовать
            //XML объект в многомерный массив, потом разложить этот массив на
            //простые массивы и проверять наличие в них нужных ключей.
//            function xmlToArray ($xml, $out = [])
//            {
//                foreach ( (array) $xml as $key => $value)
//                    $out[$key] = (is_object($value)) ||
//                    is_array($value) ?
//                    xmlToArray($value) : $value;
//
//                return $out;
//            }

//            $array = (xmlToArray($xml));

//            foreach ($array as $key => $nextLevelArray)
//            {
//                ${"$nextLevelArray{$key}"} = $nextLevelArray;
//            }


            //renaming and saving uploaded file through uploader
            $now = new \DateTime();
            $filename = $now->format('YmdHis') . '.xml';
            $uploader->upload($uploadDir, $file, $filename);


            //inserting name of uploaded file to the uploaded_files table
            $entityManager = $this->getDoctrine()->getManager();

            $newFile = new UploadedFiles();
            $newFile->setName($filename);
            $newFile->setUploadedAt($now);
            $entityManager->persist($newFile);
            $entityManager->flush();

            //sorting out the second elements (org) of xml
            foreach ($xml->org as $org)
            {
                $newOrg = $this->organizationsRepository->findOneBy(['name' => $org["displayName"]]);

                //in case the element is exists in database, don't record it
                if ($newOrg !== null)
                {
                    //sorting out user elements inside the parent element
                    foreach ($org->user as $user)
                    {
                        $newU = $this
                            ->employeesRepository
                            ->findOneBy(['firstname' => $user["firstname"],
                                'middlename' => $user["middlename"],
                                'lastname' => $user["lastname"]]);

                        //also check for existence for user element
                        //if it exists, then printing message about that
                        //if is not - recording in db
                        if ($newU !== null)
                        {
                            echo "Сотрудник " . $user["firstname"] . ' ' . $user["middlename"] . " уже записан в базу" . "<br>";
                        } else
                        {
                            $newEmployee = new Employees();
                            $newEmployee->setOrganization($this->organizationsRepository->find($newOrg->getId()));
                            $newEmployee->setFirstname($user["firstname"]);
                            $newEmployee->setMiddlename($user["middlename"]);
                            $newEmployee->setLastname($user["lastname"]);
                            $newEmployee->setInn($user["inn"]);
                            $newEmployee->setSnils($user["snils"]);

                            $entityManager->persist($newEmployee);
                            $entityManager->flush();

                            echo "Новый сотрудник " . $user["firstname"] . ' ' . $user["middlename"] . " успешно добавлен" . "<br>";
                        }
                    }
                }else
                {
                    $newOrganization = new Organizations();
                    $newOrganization->setName($org["displayName"]);
                    $newOrganization->setOgrn($org["ogrn"]);
                    $newOrganization->setOktmo($org["oktmo"]);

                    $entityManager->persist($newOrganization);
                    $entityManager->flush();

                    echo "Организация " . $org["displayName"] . ' ' . "была успешно добавлена. Все ее сотрудники так же успешно добавлены. " . '<br>';

                    foreach ($org->user as $user)
                    {
                        $newEmployee = new Employees();
                        $newEmployee->setOrganization($newOrganization);
                        $newEmployee->setFirstname($user["firstname"]);
                        $newEmployee->setMiddlename($user["middlename"]);
                        $newEmployee->setLastname($user["lastname"]);
                        $newEmployee->setInn($user["inn"]);
                        $newEmployee->setSnils($user["snils"]);

                        $entityManager->persist($newEmployee);
                        $entityManager->flush();
                    }
                }
            }

            return new Response("Файл успешно загружен, данные записаны.", Response::HTTP_OK,
                ['content-type' => 'text/plain']);
        }
        else
        {
            return new Response("Неверный тип файла", Response::HTTP_BAD_REQUEST,
                ['content-type' => 'text/plain']);
        }
    }
}