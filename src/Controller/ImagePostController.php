<?php

namespace App\Controller;

use App\Entity\ImagePost;
use App\Message\AddPonkaToImage;
use App\Message\DeleteImagePost;
use App\Photo\PhotoPonkaficator;
use App\Repository\ImagePostRepository;
use App\Photo\PhotoFileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImagePostController extends AbstractController
{
    /**
     * @Route("/api/images", methods="GET")
     * @param ImagePostRepository $repository
     * @return JsonResponse
     */
    public function list(ImagePostRepository $repository): JsonResponse
    {
        $posts = $repository->findBy([], ['createdAt' => 'DESC']);

        return $this->toJson([
            'items' => $posts
        ]);
    }

    /**
     * @Route("/api/images", methods="POST")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param PhotoFileManager $photoManager
     * @param EntityManagerInterface $entityManager
     * @param MessageBusInterface $messageBus
     * @return JsonResponse
     * @throws \Exception
     */
    public function create(
        Request $request,
        ValidatorInterface $validator,
        PhotoFileManager $photoManager,
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus
    ):JsonResponse {
        /** @var UploadedFile $imageFile */
        $imageFile = $request->files->get('file');

        $errors = $validator->validate($imageFile, [
            new Image(),
            new NotBlank()
        ]);

        if (count($errors) > 0) {
            return $this->toJson($errors, 400);
        }

        $newFilename = $photoManager->uploadImage($imageFile);
        $imagePost = new ImagePost();
        $imagePost->setFilename($newFilename);
        $imagePost->setOriginalFilename($imageFile->getClientOriginalName());

        $entityManager->persist($imagePost);
        $entityManager->flush();

        $message = new AddPonkaToImage($imagePost->getId());

        $messageBus->dispatch($message,[
            new DelayStamp(5000)
        ]);

        return $this->toJson($imagePost, 201);
    }

    /**
     * @Route("/api/images/{id}", methods="DELETE")
     * @param ImagePost $imagePost
     * @param MessageBusInterface $messageBus
     * @return Response
     */
    public function delete(
        ImagePost $imagePost,
        MessageBusInterface $messageBus
    ):Response {
        $message = new DeleteImagePost($imagePost);
        $messageBus->dispatch($message);

        return new Response(null, 204);
    }

    /**
     * @Route("/api/images/{id}", methods="GET", name="get_image_post_item")
     * @param ImagePost $imagePost
     * @return JsonResponse
     */
    public function getItem(ImagePost $imagePost): JsonResponse
    {
        return $this->toJson($imagePost);
    }

    private function toJson($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        // add the image:output group by default
        if (!isset($context['groups'])) {
            $context['groups'] = ['image:output'];
        }

        return $this->json($data, $status, $headers, $context);
    }
}
