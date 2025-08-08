<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Factory\ImageFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DefaultControllerTest extends WebTestCase
{
    use HasBrowser;
    use ResetDatabase;
    use Factories;

    public function testImageIsShownOnIndex(): void
    {
        ImageFactory::createOne(['title' => 'Woche 1', 'active' => true]);
        $this->browser()
            ->visit('/')
            ->assertSuccessful()
            ->assertSee('Woche 1')
            ->assertNotSee('Älterer Eintrag')
        ;
    }

    public function testImagesOnOverview(): void
    {
        ImageFactory::createOne(['title' => 'Woche 1', 'active' => true]);
        ImageFactory::createOne(['title' => 'Woche 2', 'active' => true]);
        ImageFactory::createOne(['title' => 'Woche 3', 'active' => true]);
        ImageFactory::createOne(['title' => 'Woche 4', 'active' => false]);

        $this->browser()
            ->visit('/overview')
            ->assertSuccessful()
            ->assertElementCount('.image-card', 3)
        ;
    }

    public function testMultipleActiveImagesAreShown(): void
    {
        ImageFactory::createOne(['title' => 'Woche 1', 'active' => true]);
        ImageFactory::createOne(['title' => 'Woche 2', 'active' => true]);
        ImageFactory::createOne(['title' => 'Woche 3', 'active' => true]);
        ImageFactory::createOne(['title' => 'Woche 4', 'active' => false]);

        $this->browser()
            ->visit('/')
            ->assertSuccessful()
            ->assertSee('Woche 3')
            ->click('Älterer Eintrag')
            ->assertSuccessful()
            ->assertSee('Woche 2')
            ->click('Älterer Eintrag')
            ->assertSuccessful()
            ->assertSee('Woche 1')
            ->assertNotSee('Älterer Eintrag')
        ;
    }

    public function testNoActiveImageOnIndex(): void
    {
        ImageFactory::createOne(['title' => 'Woche 1', 'active' => false]);
        $this->browser()
            ->visit('/')
            ->assertSuccessful()
            ->assertSee('Kein Bild vorhanden')
        ;
    }

    public function testImagesOnRss(): void
    {
        ImageFactory::createOne(['title' => 'Woche 1', 'active' => true]);
        ImageFactory::createOne(['title' => 'Woche 2', 'active' => true]);
        ImageFactory::createOne(['title' => 'Woche 3', 'active' => true]);
        ImageFactory::createOne(['title' => 'Woche 4', 'active' => false]);

        $this->browser()
            ->visit('/rss')
            ->assertSuccessful()
            ->assertXml()
            ->use(function (Browser $browser) {
                $xml = \simplexml_load_string($browser->content());
                $this->assertInstanceOf(\SimpleXMLElement::class, $xml);
                $this->assertCount(3, $xml->channel->item);
            })
        ;
    }

    public function testAdminRedirect(): void
    {
        $this->browser()
            ->interceptRedirects()
            ->visit('/admin')
            ->assertRedirectedTo('/login')
            ->assertSuccessful();
    }
}
