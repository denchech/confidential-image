<?php


namespace App\Service;


use App\Entity\Image;

class TextConverter
{
    private const SIZE = 14;
    private const ANGLE = 0;

    private $font;
    private $imagesDirectory;
    private $image;
    private $filepath;

    public function __construct($imagesDirectory, $fontsDirectory)
    {
        $this->font = $fontsDirectory . 'arial.ttf';
        $this->imagesDirectory = $imagesDirectory;
    }

    public function convert(string $text): string
    {
        $size = imagettfbbox(self::SIZE, self::ANGLE, $this->font, $text);
        $xSize = abs($size[0]) + abs($size[2]);
        $ySize = abs($size[5]) + abs($size[1]);

        $this->image = imagecreate($xSize, $ySize);
        $white = imagecolorallocate($this->image, 255, 255, 255);
        $black = imagecolorallocate($this->image, 0, 0, 0);

        imagefilledrectangle($this->image, 0, 0, $xSize, $ySize, $white);
        imagettftext($this->image,
            self::SIZE,
            self::ANGLE,
            abs($size[0]),
            abs($size[5]),
            $black,
            $this->font,
            $text);

        $file = $this->changeFilename();
        while (file_exists($file['filepath'])) {
            $file = $this->changeFilename();
        }
        $this->filepath = $file['filepath'];

        return $file['uuid'];
    }

    public function save(): bool
    {
        if (!$this->image) {
            return false;
        }
        imagepng($this->image, $this->filepath);
        imagedestroy($this->image);
        return true;
    }

    private function changeFilename(): array
    {
        $uuid = uniqid();
        $filepath = $this->imagesDirectory . $uuid . Image::getExtension();
        return [
            'uuid' => $uuid,
            'filepath' => $filepath
        ];
    }
}