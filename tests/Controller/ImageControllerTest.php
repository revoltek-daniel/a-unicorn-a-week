<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Factory\ImageFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ImageControllerTest extends WebTestCase
{
    use HasBrowser;
    use ResetDatabase;
    use Factories;

    public function testListImages(): void
    {
        $user = UserFactory::createOne();

        ImageFactory::createMany(5);

        $this->browser()
            ->actingAs($user)
            ->visit('/admin/image/')
            ->assertSuccessful()
            ->assertElementCount('tr', 6)
        ;
    }

    public function testShowImage(): void
    {
        $user = UserFactory::createOne();

        $image = ImageFactory::createOne();
        $this->browser()
            ->actingAs($user)
            ->visit('/admin/image/' . $image->getId())
            ->assertSuccessful()
            ->assertSee($image->getTitle())
            ->assertSee((string)$image->getDescription())
            ->assertSee($image->getCreated()->format('d.m.Y'))
        ;
    }

    public function testNewImage(): void
    {
        $user = UserFactory::createOne();

        $this->browser()
            ->actingAs($user)
            ->visit('/admin/image/new')
            ->assertSuccessful()
            ->assertSee('Neues Bild erstellen')
            ->fillField('unicorn_image[title]', 'new_image')
            ->fillField('unicorn_image[description]', 'new_description')
            ->fillField('unicorn_image[image]', __DIR__ . '/../fixtures/images/testBild.jpeg')
            ->fillField('unicorn_image[active]', '1')
            ->interceptRedirects()
            ->click('Erstellen')
            ->assertRedirectedTo('/admin/image/1')
        ;
    }

    public function testEditImage(): void
    {
        $this->markTestSkipped('Not implemented yet');
        $user = UserFactory::createOne();

        $image = ImageFactory::createOne();
        $this->browser()
            ->actingAs($user)
            ->visit('/admin/image/' . $image->getId() . '/edit')
            ->assertSuccessful()
        ;
    }

    public function testDeleteImage(): void
    {
        $user = UserFactory::createOne();
        $image = ImageFactory::createOne();

        $this->browser()
            ->actingAs($user)
            ->interceptRedirects()
            ->visit('/admin/image/' . $image->getId())
            ->click('Delete')
            ->assertRedirectedTo('/admin/image/')
            ->assertSuccessful()
            ->assertElementCount('tr', 1)
            ->assertNotSee($image->getTitle())
        ;
    }
}
