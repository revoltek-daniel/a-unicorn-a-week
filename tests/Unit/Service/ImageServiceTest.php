<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\ImageService;
use Imagine\Gmagick\Imagine;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageServiceTest extends TestCase
{
    public function testRotation90(): void
    {
        $imagine = new Imagine();

        $imageService = new ImageService(
            $imagine,
            'public/uploads/images',
            __DIR__ . '/../../../'
        );

        // test rotated image 90 degrees
        $uploadedFile = new UploadedFile(
            __DIR__ . '/../../fixtures/images/testBild-rotated-90.jpeg',
            'testBild-rotated-90.jpeg',
            'image/jpeg',
            null,
            true
        );

        // caution: image will be moved
        $filename = $imageService->uploadNewPicture($uploadedFile, 1);

        $this->assertFileExists(__DIR__ . '/../../../public/uploads/images/' . $filename);
    }

    public function testRotation180(): void
    {
        $imagine = new Imagine();

        $imageService = new ImageService(
            $imagine,
            'public/uploads/images',
            __DIR__ . '/../../../'
        );

        // test rotated image 180 degrees
        $uploadedFile = new UploadedFile(
            __DIR__ . '/../../fixtures/images/testBild-rotated-180.jpeg',
            'testBild-rotated-180.jpeg',
            'image/jpeg',
            null,
            true
        );

        // caution: image will be moved
        $filename = $imageService->uploadNewPicture($uploadedFile, 1);

        $this->assertFileExists(__DIR__ . '/../../../public/uploads/images/' . $filename);
    }

    public function testRotation270(): void
    {
        $imagine = new Imagine();

        $imageService = new ImageService(
            $imagine,
            'public/uploads/images',
            __DIR__ . '/../../../'
        );

        // test rotated image 270 degrees
        $uploadedFile = new UploadedFile(
            __DIR__ . '/../../fixtures/images/testBild-rotated-270.jpeg',
            'testBild-rotated-270.jpeg',
            'image/jpeg',
            null,
            true
        );

        // caution: image will be moved
        $filename = $imageService->uploadNewPicture($uploadedFile, 1);

        $this->assertFileExists(__DIR__ . '/../../../public/uploads/images/' . $filename);
    }
}
