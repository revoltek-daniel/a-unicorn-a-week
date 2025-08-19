<?php

namespace App\Service;

use Imagine\Image\ImagineInterface;
use Random\RandomException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService
{
    /**
     * @var array<string>
     */
    protected array $allowedFiletypes = ['image/jpeg', 'image/png', 'image/heic'];

    /**
     * @var array<string>
     */
    protected array $nonConvertableFiletypes = ['image/jpeg', 'image/png'];

    public function __construct(
        protected ImagineInterface $imagine,
        protected string $filepath,
        protected string $rootPath,
    ) {
    }

    public function uploadNewPicture(UploadedFile $file, ?int $imageId): string
    {
        if (!in_array($file->getMimeType(), $this->allowedFiletypes, true)) {
            throw new \RuntimeException('daniel.admin.error.picture.invalid');
        }

        try {
            $filename = \sha1(random_int(0, 50) . $imageId . random_int(0, 50) . $file->getClientOriginalName() . random_int(0, 50));
        } catch (RandomException) {
            throw new \RuntimeException('daniel.admin.error.picture.invalid');
        }

        $exifMetadataReader = $this->imagine->getMetadataReader();
        $exifData = $exifMetadataReader->readFile($file->getPathname());

        if (
            isset($exifData['ifd0.Orientation']) &&
            (
                $exifData['ifd0.Orientation'] !== 0 &&
                $exifData['ifd0.Orientation'] !== 1 &&
                $exifData['ifd0.Orientation'] !== 2
            )
        ) {
            $file = $this->rotateImage($file, $exifData['ifd0.Orientation']);
        }

        if (in_array($file->getMimeType(), $this->nonConvertableFiletypes, true) === false) {
            $filename .= '.jpg';
            $this->convertImage($file, $filename);
        } else {
            $filename .= '.' . $file->getClientOriginalExtension();
            $file->move($this->filepath, $filename);
        }

        return $filename;
    }

    /**
     * Remove Old File.
     *
     * @param string $oldFile
     *
     * @return void
     */
    public function removeOldPicture(string $oldFile): void
    {
        $file = $this->filepath . DIRECTORY_SEPARATOR . $oldFile;
        if ($oldFile && file_exists($file)) {
            unlink($file);
            $thumb = $this->filepath . DIRECTORY_SEPARATOR . 'thumb' . DIRECTORY_SEPARATOR . $oldFile;
            if (file_exists($thumb)) {
                unlink($thumb);
            }
        }
    }

    public function convertImage(UploadedFile $file, string $filename): void
    {
        $filepath = $file->getPathname();

        $filename = $this->rootPath . '/public/' . $this->filepath . '/' . $filename;
        $this->imagine->open($filepath)
            ->save($filename);
    }

    private function rotateImage(UploadedFile $file, int $orientation): UploadedFile
    {
        $image = $this->imagine->open($file->getPathname());

        switch ($orientation) {
            case 3:
            case 4:
                $angle = 180;
                break;
            case 5:
            case 6:
                $angle = 90;
                break;
            case 7:
            case 8:
                $angle = 270;
                break;
            default:
                $angle = 0;
        }

        $image->rotate($angle);
        $image->save($file->getPathname());

        return $file;
    }
}
