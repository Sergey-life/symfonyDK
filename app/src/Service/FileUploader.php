<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $imageDirectory;
//    private $slugger;

    public function __construct($imageDirectory, SluggerInterface $slugger)
    {
        $this->imageDirectory = $imageDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file)
    {
//        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
//        $safeImageName = $this->slugger->slug($originalFilename);
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        try {
            $file->move(
                $this->getImageDirectory(),
                $fileName
            );
        } catch (FileException $e) {
            return new Response('При завантаженні зображення сталася помилка!'.$e);
        }

        return $fileName;
    }

    public function getImageDirectory()
    {
        return $this->imageDirectory;
    }
}