<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiTest extends WebTestCase
{
    protected KernelBrowser $client;

    protected static $testUser = [
        'username' => 'test_user',
        'password' => 'test1234',
        'roles' => ['ROLE_USER'],
    ];
    protected static $testAdmin = [
        'username' => 'test_admin',
        'password' => 'test1234',
        'roles' => ['ROLE_ADMIN'],
    ];

    protected static $testUserToken;
    protected static $testAdminToken;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $command = $application->find('app:user-create');
        $output = new BufferedOutput();
        $input = new ArrayInput(static::$testUser);
        $command->run($input, $output);
        $input = new ArrayInput(static::$testAdmin);
        $command->run($input, $output);

        $response = $this->post('/auth', static::$testUser);
        static::$testUserToken = json_decode($response->getContent(), true)['token'];
        $response = $this->post('/auth', static::$testAdmin);
        static::$testAdminToken = json_decode($response->getContent(), true)['token'];
    }

    protected function get(string $uri, string $token = null): Response
    {
        $this->client->request('GET', $uri, [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => $token ? 'Bearer ' . $token : null,
        ]);

        return $this->client->getResponse();
    }

    protected function post(string $uri, array $data, string $token = null): Response
    {
        $this->client->request('POST', $uri, [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => $token ? 'Bearer ' . $token : null,
        ], json_encode($data));

        return $this->client->getResponse();
    }

    protected function put(string $uri, array $data, string $token = null): Response
    {
        $this->client->request('PUT', $uri, [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => $token ? 'Bearer ' . $token : null,
        ], json_encode($data));

        return $this->client->getResponse();
    }

    protected function delete(string $uri, string $token = null): Response
    {
        $this->client->request('DELETE', $uri, [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => $token ? 'Bearer ' . $token : null,
        ]);

        return $this->client->getResponse();
    }
}
