<?php


namespace App\Service;


use Symfony\Component\HttpKernel\KernelInterface;

class TextConverter
{
    private const SIZE = 14;
    private const ANGLE = 0;

    private $font;
    private $imagesDirectory;

    public function __construct(KernelInterface $kernel)
    {
        $this->font = $kernel->getProjectDir() . '/fonts/arial.ttf';
        $this->imagesDirectory = $kernel->getProjectDir() . '/images/';
    }

    public function convert(string $text, string $filename) {
        $size = imagettfbbox(self::SIZE, self::ANGLE, $this->font, $text);
        $xSize = abs($size[0]) + abs($size[2]);
        $ySize = abs($size[5]) + abs($size[1]);

        $image = imagecreate($xSize, $ySize);
        $white = imagecolorallocate($image, 255,255,255);
        $black = imagecolorallocate($image, 0,0,0);

        imagefilledrectangle($image, 0, 0, $xSize, $ySize, $white);
        imagettftext($image,
            self::SIZE,
            self::ANGLE,
            abs($size[0]),
            abs($size[5]),
            $black,
            $this->font,
            $text);

        $filepath = $this->imagesDirectory . $filename . '.png';
        imagepng($image, $filepath);
        imagedestroy($image);
        return $filepath;
    }
}