<?php


namespace App\Service;


use App\Entity\Image;

class TextConverter
{
    private const SIZE = 14;
    private const ANGLE = 0;
    private const MAX_LETTERS_IN_STRING = 55;

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
        $text = self::splitText($text);
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

    private static function splitText(string $text): string
    {
        $newText = "";
        $text = preg_replace('/[ ]+/', ' ', $text);
        $splitTextArray = explode("\n", $text);
        foreach ($splitTextArray as $str) {
            $trimStr = trim($str, " ");
            if ($trimStr === "\n" || $trimStr === "\r") {
                $newText .= "\n\n";
                continue;
            }
            $splitString = explode(' ', $str);
            $currentStr = "";
            foreach ($splitString as $word) {
                $word .= " ";
                if (strlen($currentStr) + strlen($word) < self::MAX_LETTERS_IN_STRING) {
                    $currentStr .= $word;
                } else {
                    if (strlen($word) > self::MAX_LETTERS_IN_STRING) {
                        $newText .= $currentStr . "\n" . substr($word, 0, self::MAX_LETTERS_IN_STRING);
                        $currentStr = "\n" . substr($word, self::MAX_LETTERS_IN_STRING - strlen($word));
                    } else {
                        $newText .= $currentStr;
                        $currentStr = "\n" . $word;
                    }
                }
            }
            $newText .= trim($currentStr, ' ');
        }
        $newText = preg_replace('/\r+/', "\n", $newText);
        $newText = preg_replace('/[\n]{3,}/', "\n\n", $newText);
        return $newText;
    }
}