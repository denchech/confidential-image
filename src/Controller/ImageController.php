<?php

namespace App\Controller;

use App\Entity\Image;
use App\Service\ImageHelper;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;

class ImageController extends AbstractController
{
    /**
     * @Route("/image/{uuid}", name="app_image_show")
     * @IsGranted("ROLE_USER")
     */
    public function show(Image $image,
                         EntityManagerInterface $em,
                         ImageHelper $imageHelper)
    {
        session_destroy();
        $now = new \DateTime("midnight", new \DateTimeZone("UTC"));
        if ($image->getExpiresAt() < $now || ($image->getMaxOpeningsNumber() !== null &&
                $image->open($this->generateUrl('app_image_show',
                    ['uuid' => $image->getUuid()],
                    UrlGenerator::ABSOLUTE_URL)) > $image->getMaxOpeningsNumber())) {
            $imageHelper->delete([$image]);
            return $this->render('image/show.html.twig');
        }
        $em->flush();
        return $this->render('image/show.html.twig', [
            'image' => $image,
        ]);
    }
}
