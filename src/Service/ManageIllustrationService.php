<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ManageIllustrationService
{
    private string $targetDirectory = '';

    public function __construct (string $targetDirectory = '') {
        $this->targetDirectory = $targetDirectory;
    }

    public function manageIllustration(UploadedFile $file) : string
    {
        $filename = $this->buildFileName($file);
        $this->uploadFile($file, $filename);

        return $filename;
    }

    private function uploadFile(UploadedFile $file, string $filename) : void
    {
        try {
            $file->move(
                $this->targetDirectory,
                $filename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
    }

    /**
     * @param UploadedFile $file
     *
     * @return string
     */
    private function buildFileName(UploadedFile $file) : string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = Tools::slugify($originalFilename);
        return $safeFilename . '.' .$file->guessExtension();
    }
}
