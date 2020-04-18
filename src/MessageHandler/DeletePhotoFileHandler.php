<?php


namespace App\MessageHandler;

use App\Message\DeletePhotoFile;
use App\Photo\PhotoFileManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DeletePhotoFileHandler implements MessageHandlerInterface
{
    /**
     * @var PhotoFileManager
     */
    private $photoFileManager;

    public function __construct(PhotoFileManager $photoFileManager)
    {
        $this->photoFileManager = $photoFileManager;
    }

    public function __invoke(DeletePhotoFile $deletePhotoFile)
    {
        $this->photoFileManager->deleteImage($deletePhotoFile->getFilename());
    }
}