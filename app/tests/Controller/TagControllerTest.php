<?php

/*
 * Tag controller service test.
 */

namespace App\Tests\Controller;

use App\Entity\Tag;
use App\Entity\User;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TagControllerTest.
 */
class TagControllerTest extends WebTestCase
{
    private const TEST_ROUTE_INDEX = '/tag';

    private KernelBrowser $client;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test index() for anonymous user.
     */
    public function testIndexRouteAnonymousUser(): void
    {
        $this->client->request('GET', self::TEST_ROUTE_INDEX);
        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * Test index() for admin user.
     */
    public function testIndexRouteAdminUser(): void
    {
        $adminUser = $this->createUser(['ROLE_USER', 'ROLE_ADMIN']);
        $this->client->loginUser($adminUser);

        $this->client->request('GET', self::TEST_ROUTE_INDEX);
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test index() for non-admin user.
     */
    public function testIndexRouteNonAdminUser(): void
    {
        $user = $this->createUser(['ROLE_USER']);
        $this->client->loginUser($user);

        $this->client->request('GET', self::TEST_ROUTE_INDEX);
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * Test show() for admin user.
     */
    public function testShowRouteAdminUser(): void
    {
        $adminUser = $this->createUser(['ROLE_ADMIN', 'ROLE_USER']);
        $this->client->loginUser($adminUser);

        $tag = new Tag();
        $tag->setTitle('Test Tag');
        $tagRepository = static::getContainer()->get(TagRepository::class);
        $tagRepository->save($tag);

        $this->client->request('GET', '/tag/'.$tag->getId());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Szczegóły tagu');
    }

    /**
     * Test show() for non-admin user.
     */
    public function testShowRouteNonAdminUser(): void
    {
        $user = $this->createUser(['ROLE_USER']);
        $this->client->loginUser($user);

        $tagRepository = static::getContainer()->get(TagRepository::class);

        $tag = $tagRepository->findAll()[0] ?? null;

        if (null === $tag) {
            $tag = new Tag();
            $tag->setTitle('Temporary Test Tag');
            $tagRepository->save($tag);
        }

        $this->client->request('GET', '/tag/'.$tag->getId());
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * Test create tag.
     */
    public function testCreateTag(): void
    {
        $adminUser = $this->createUser(['ROLE_ADMIN', 'ROLE_USER']);
        $this->client->loginUser($adminUser);

        $this->client->request('POST', '/tag/create', [
            'tag' => [
                'title' => 'New Test Tag Without Form',
            ],
        ]);

        $this->assertResponseRedirects('/tag');

        $this->client->followRedirect();

        $tagRepository = static::getContainer()->get(TagRepository::class);
        $savedTag = $tagRepository->findOneBy(['title' => 'New Test Tag Without Form']);
        $this->assertNotNull($savedTag);
    }

    /**
     * Test edit tag.
     */
    public function testEditTag(): void
    {
        $adminUser = $this->createUser(['ROLE_ADMIN', 'ROLE_USER']);
        $this->client->loginUser($adminUser);

        $tagRepository = static::getContainer()->get(TagRepository::class);

        $tag = new Tag();
        $tag->setTitle('Tag to Edit');
        $tagRepository->save($tag);

        $postData = [
            'tag' => [
                'title' => 'Edited Tag Title',
            ],
            '_method' => 'PUT',
        ];

        $this->client->request('POST', '/tag/'.$tag->getId().'/edit', $postData);

        $this->assertResponseRedirects('/tag');

        $this->client->followRedirect();

        $updatedTag = $tagRepository->find($tag->getId());
        $this->assertSame('Edited Tag Title', $updatedTag->getTitle());
    }

    /**
     * Test delete tag.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testDeleteTag(): void
    {
        $adminUser = $this->createUser(['ROLE_ADMIN', 'ROLE_USER']);
        $this->client->loginUser($adminUser);

        $tagRepository = static::getContainer()->get(TagRepository::class);

        $tag = new Tag();
        $tag->setTitle('Tag to Delete');
        $tagRepository->save($tag);

        $tagId = $tag->getId();

        $this->client->request('POST', '/tag/'.$tagId.'/delete', [
            '_method' => 'DELETE',
            'form' => [],
        ]);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/tag');
        $this->assertNull($tagRepository->find($tagId));
    }

    /**
     * Create user helper.
     *
     * @param array $roles roles
     *
     * @return User User
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createUser(array $roles): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $user = new User();
        $user->setEmail('user_'.uniqid().'@example.com');
        $user->setRoles($roles);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                'p@55w0rd'
            )
        );

        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->save($user);

        return $user;
    }
}
