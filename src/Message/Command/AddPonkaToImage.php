<?php


namespace App\Message\Command;


class AddPonkaToImage
{
    /**
     * @var int
     */
    private $imageId;

    public function __construct(int $imageId)
    {
        $this->imageId = $imageId;
    }

    public function getImageId(): int
    {
        return $this->imageId;
    }
}