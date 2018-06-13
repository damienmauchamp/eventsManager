<?php
/**
 * Created by PhpStorm.
 * User: damien
 * Date: 13/06/2018
 * Time: 11:10
 */

namespace App;

use Symfony\Component\HttpFoundation\File\UploadedFile;


class ImageUpload
{
    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $file->move($this->getTargetDir(), $fileName);

        return $fileName;
    }

    public function getTargetDir()
    {
        return $this->targetDir;
    }
}