<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

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

    public function testEdit(): void
    {
        $user = UserFactory::createOne(['Roles' => ['ROLE_USER', 'ROLE_ADMIN']]);
        $user2 = UserFactory::createOne(['Roles' => ['ROLE_USER', 'ROLE_ADMIN'], 'isActive' => false]);
        $this->browser()
            ->actingAs($user)
            ->visit('/admin/user/' . $user2->getId() . '/edit')
            ->assertSuccessful()
            ->assertSee('User edit')
            ->fillField('unicorn_user[username]', 'new_user')
            ->fillField('unicorn_user[plainPassword]', '123456')
            ->fillField('unicorn_user[isActive]', '1')
            ->interceptRedirects()
            ->click('Edit')
            ->assertRedirectedTo('/admin/user/' . $user2->getId())
            ->assertSuccessful()
            ->assertSee('new_user')
        ;
    }

    public function testDelete(): void
    {
        $user = UserFactory::createOne(['Roles' => ['ROLE_USER', 'ROLE_ADMIN']]);
        $user2 = UserFactory::createOne(['Roles' => ['ROLE_USER']]);

        $this->browser()
            ->actingAs($user)
            ->interceptRedirects()
            ->visit('/admin/user/' . $user2->getId())
            ->click('Delete')
            ->assertRedirectedTo('/admin/user/')
            ->assertSuccessful()
            ->assertNotSee($user2->getUsername())
            ->assertElementCount('tr', 2)
        ;
    }

    public function testNew(): void
    {
        $user = UserFactory::createOne(['Roles' => ['ROLE_USER', 'ROLE_ADMIN']]);
        $this->browser()
            ->actingAs($user)
            ->visit('/admin/user/new')
            ->assertSuccessful()
            ->assertSee('User creation')
            ->fillField('unicorn_user[username]', 'new_user')
            ->fillField('unicorn_user[plainPassword]', '123456')
            ->fillField('unicorn_user[isActive]', '1')
            ->interceptRedirects()
            ->click('Create')
            ->assertRedirectedTo('/admin/user/2')
            ->assertSuccessful()
            ->assertSee('new_user')
        ;
    }
}
