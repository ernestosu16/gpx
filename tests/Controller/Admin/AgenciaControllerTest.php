<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Trabajador;
use App\Repository\TrabajadorCredencialRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AgenciaControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();

        // retrieve the test user
        $testUser = static::getContainer()->get(TrabajadorCredencialRepository::class)->findOneByUsuario('admin');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/admin/agencia/');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, $crawler->filter('div[class="panel-heading hbuilt"]')->count());
        $this->assertSelectorTextContains('div', 'Sistema de Gestión Paquetería Express Bienvenido al Sistema de Gestión Paquetería Express');
    }
}
