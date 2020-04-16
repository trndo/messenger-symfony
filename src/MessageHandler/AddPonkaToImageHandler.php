<?php


namespace App\MessageHandler;


use App\Message\AddPonkaToImage;
use App\Photo\PhotoFileManager;
use App\Photo\PhotoPonkaficator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddPonkaToImageHandler implements MessageHandlerInterface
{
    /**
     * @var PhotoPonkaficator
     */
    private $photoPonkaficator;
    /**
     * @var PhotoFileManager
     */
    private $fileManager;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        PhotoPonkaficator $photoPonkaficator,
        PhotoFileManager $fileManager,
        EntityManagerInterface $entityManager
    ) {
        $this->photoPonkaficator = $photoPonkaficator;
        $this->fileManager = $fileManager;
        $this->entityManager = $entityManager;
    }

    public function __invoke(AddPonkaToImage $addPonkaToImage)
    {
        $imagePost = $addPonkaToImage->getImagePost();

        $updatedContents = $this->photoPonkaficator->ponkafy(
            $this->fileManager->read($imagePost->getFilename())
        );
        $this->fileManager->update($imagePost->getFilename(), $updatedContents);
        $imagePost->markAsPonkaAdded();
        $this->entityManager->flush();
    }
}