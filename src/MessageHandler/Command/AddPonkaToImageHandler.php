<?php


namespace App\MessageHandler\Command;

use App\Entity\ImagePost;
use App\Message\Command\AddPonkaToImage;
use App\Photo\PhotoFileManager;
use App\Photo\PhotoPonkaficator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddPonkaToImageHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

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

    private $repository;

    public function __construct(
        PhotoPonkaficator $photoPonkaficator,
        PhotoFileManager $fileManager,
        EntityManagerInterface $entityManager
    ) {
        $this->photoPonkaficator = $photoPonkaficator;
        $this->fileManager = $fileManager;
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(ImagePost::class);
    }

    public function __invoke(AddPonkaToImage $addPonkaToImage)
    {
        $imageId = $addPonkaToImage->getImageId();
        $imagePost = $this->repository->find($imageId);

        if (!$imagePost) {

            if ($this->logger) {
                $this->logger->alert("Image post $imageId was missing");
            }

            return;
        }

        $updatedContents = $this->photoPonkaficator->ponkafy(
            $this->fileManager->read($imagePost->getFilename())
        );

        $this->fileManager->update($imagePost->getFilename(), $updatedContents);
        $imagePost->markAsPonkaAdded();
        $this->entityManager->flush();
    }
}