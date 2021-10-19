<?php

namespace App\Tests\Controller\Admin;

use App\Repository\TrabajadorCredencialRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AdminWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $client = static::createClient();

        # retrieve the test user
        $testUser = static::getContainer()->get(TrabajadorCredencialRepository::class)->findOneByUsuario('admin');

        # simulate $testUser being logged in
        $this->client = $client->loginUser($testUser);
    }
}