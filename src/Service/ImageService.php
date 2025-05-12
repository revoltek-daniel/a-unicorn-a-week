<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Imagine\Image\ImagineInterface;
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
        protected EntityManagerInterface $entityManager,
        protected ImagineInterface $imagine,
        protected string $filepath,
        protected string $rootPath,
    ) {
    }

    public function uploadNewPicture(UploadedFile $file, ?int $imageId): string
    {
        if (!in_array($file->getMimeType(), $this->allowedFiletypes)) {
            throw new \RuntimeException('daniel.admin.error.picture.invalid');
        }

        $filename = sha1(random_int(0, 50) . $imageId . random_int(0, 50) . $file->getClientOriginalName() . random_int(0, 50));

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
}
