<?php
namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader 
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @param UploadedFile $file
     * @return boolean|string
     */
    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            return false;
        }

        return $fileName;
    }
    
    /**
     * @param type $fileName
     * @return boolean
     */
    public function delete($fileName)
    {
        try {
            if (!empty($fileName) && file_exists($this->targetDirectory . '/' . $fileName)) {
                unlink($this->targetDirectory . '/' . $fileName);
            }
        } catch (FileException $e) {
            throw $e;
            return false;
        }
        
        return true;
        
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
