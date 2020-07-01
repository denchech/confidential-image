<?php


namespace App\Service;


use App\Entity\Image;

class ImageHelper
{
    private $imagesDirectory;

    public function __construct($imagesDirectory)
    {
        $this->imagesDirectory = $imagesDirectory;
    }

    public function getImagePath(Image $image)
    {
        return $this->imagesDirectory . $image->getFilename();
    }
}