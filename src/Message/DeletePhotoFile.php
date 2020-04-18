<?php


namespace App\Message;


use App\Entity\ImagePost;

class DeletePhotoFile
{
    /**
     * @var string
     */
    private $fileName;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}