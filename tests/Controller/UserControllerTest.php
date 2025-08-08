<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserControllerTest extends WebTestCase
{
    use HasBrowser;
    use ResetDatabase;
    use Factories;

    public function testIndex(): void
    {
        $user = UserFactory::createOne(['Roles' => ['ROLE_USER', 'ROLE_ADMIN']]);
        UserFactory::createMany(2);

        $this->browser()
            ->actingAs($user)
            ->visit('/admin/user')
            ->assertSuccessful()
            ->assertSee('Users list')
            ->assertElementCount('tr', 4)
        ;
    }

    public function testShow(): void
    {
        $user = UserFactory::createOne(['Roles' => ['ROLE_USER', 'ROLE_ADMIN']]);
        $this->browser()
            ->actingAs($user)
            ->visit('/admin/user/' . $user->getId())
            ->assertSuccessful()
            ->assertSee('User')
        ;
    }
}
