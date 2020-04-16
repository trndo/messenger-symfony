<?php


namespace App\MessageHandler;


use App\Message\DeleteImagePost;
use App\Photo\PhotoFileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DeleteImagePostHandler implements MessageHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var PhotoFileManager
     */
    private $photoFileManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        PhotoFileManager $photoFileManager
    ) {
        $this->entityManager = $entityManager;
        $this->photoFileManager = $photoFileManager;
    }

    public function __invoke(DeleteImagePost $deleteImagePost)
    {
        $imagePost = $deleteImagePost->getImagePost();

        $this->photoFileManager->deleteImage($imagePost->getFilename());

        $this->entityManager->remove($imagePost);
        $this->entityManager->flush();
    }
}