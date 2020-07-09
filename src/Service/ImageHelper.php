<?php


namespace App\Service;


use App\Entity\Image;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class ImageHelper
{
    private $imagesDirectory;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct($imagesDirectory, EntityManagerInterface $em)
    {
        $this->imagesDirectory = $imagesDirectory;
        $this->em = $em;
    }

    /**
     * @param $images ArrayCollection|array
     */
    public function delete($images)
    {
        foreach ($images as $image) {
            $this->em->remove($image);
        }
        $this->em->flush();
        foreach ($images as $image) {
            unlink($this->getImagePath($image));
        }
    }

    public function getImagePath(Image $image)
    {
        return $this->imagesDirectory . $image->getFilename();
    }
}