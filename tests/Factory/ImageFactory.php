<?php

namespace App\Tests\Factory;

use App\Entity\Image;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Image>
 */
final class ImageFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Image::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'active' => self::faker()->boolean(),
            'description' => self::faker()->text(),
            'image' => self::faker()->file(__DIR__ . '/../fixtures/images', 'public/uploads/images', false),
            'sorting' => self::faker()->randomNumber(),
            'title' => self::faker()->text(),
        ];
    }
}
