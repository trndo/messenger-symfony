<?php


namespace App\Messenger\Event;


use App\Message\Event\ImagePostDeletedEvent;
use App\Photo\PhotoFileManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveFileWhenImagePostDeleted implements MessageHandlerInterface
{
    /**
     * @var PhotoFileManager
     */
    private $photoFileManager;

    /**
     * RemoveFileWhenImagePostDeleted constructor.
     * @param PhotoFileManager $photoFileManager
     */
    public function __construct(PhotoFileManager $photoFileManager)
    {
        $this->photoFileManager = $photoFileManager;
    }

    public function __invoke(ImagePostDeletedEvent $imagePostDeletedEvent)
    {
        $this->photoFileManager->deleteImage($imagePostDeletedEvent->getFilename());
    }
}