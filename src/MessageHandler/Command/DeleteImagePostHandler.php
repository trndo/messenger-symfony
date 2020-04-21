<?php


namespace App\MessageHandler\Command;


use App\Message\Command\DeleteImagePost;
use App\Message\Event\ImagePostDeletedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteImagePostHandler implements MessageHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MessageBusInterface
     */
    private $eventBus;

    public function __construct(
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus
    ) {
        $this->entityManager = $entityManager;
        $this->eventBus = $eventBus;
    }

    public function __invoke(DeleteImagePost $deleteImagePost)
    {
        $imagePost = $deleteImagePost->getImagePost();
        $fileName = $imagePost->getFilename();

        $this->entityManager->remove($imagePost);
        $this->entityManager->flush();

        $this->eventBus->dispatch(new ImagePostDeletedEvent($fileName));
    }
}