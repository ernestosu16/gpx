<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Agencia;
use App\Repository\TrabajadorCredencialRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AgenciaControllerTest extends WebTestCase
{
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;

    protected function setUp(): void
    {
        $client = static::createClient();

        # retrieve the test user
        $testUser = static::getContainer()->get(TrabajadorCredencialRepository::class)->findOneByUsuario('admin');

        # simulate $testUser being logged in
        $this->client = $client->loginUser($testUser);
    }

    public function testLista(): void
    {
        $crawler = $this->client->request('GET', '/admin/agencia/');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, $crawler->filter('div[class="panel-heading hbuilt"]')->count());
        $this->assertSelectorTextContains('div', 'Sistema de Gestión Paquetería Express Bienvenido al Sistema de Gestión Paquetería Express');
    }

    public function testCrear(): void
    {
        $crawler = $this->client->request('GET', '/admin/agencia/new');
        $buttonCrawlerNode = $crawler->selectButton('Guardar');

        $form = $buttonCrawlerNode->form();

        # set values on a form object
        $form['agencia[codigo]'] = 'agencia_symfony';
        $form['agencia[nombre]'] = 'symfony';
        $form['agencia[descripcion]'] = 'Symfony rocks!';
        $form['agencia[habilitado]'] = true;

        # submit the Form object
        $this->client->submit($form);

        $this->assertEquals(303, $this->client->getResponse()->getStatusCode());
    }

    public function testEditar(): void
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $agencia = $em->getRepository(Agencia::class)->findOneByCodigo('agencia_symfony');
        $crawler = $this->client->request('GET', '/admin/agencia/' . $agencia->getId() . '/edit');
        $buttonCrawlerNode = $crawler->selectButton('Guardar');

        $form = $buttonCrawlerNode->form();

        # set values on a form object
        $form['agencia[codigo]'] = 'agencia_symfony';
        $form['agencia[nombre]'] = 'symfony x';
        $form['agencia[descripcion]'] = 'Symfony rocks!';
        $form['agencia[habilitado]'] = true;

        # submit the Form object
        $this->client->submit($form);

        $this->assertEquals(303, $this->client->getResponse()->getStatusCode());
    }

    public function testEliminar(): void
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $agencia = $em->getRepository(Agencia::class)->findOneByCodigo('agencia_symfony');
        $crawler = $this->client->request('GET', '/admin/agencia/' . $agencia->getId() . '/edit');
        $buttonCrawlerNode = $crawler->selectButton('Eliminar');

        $form = $buttonCrawlerNode->form();

        # submit the Form object
        $this->client->submit($form);

        $this->assertEquals(303, $this->client->getResponse()->getStatusCode());
    }
}
