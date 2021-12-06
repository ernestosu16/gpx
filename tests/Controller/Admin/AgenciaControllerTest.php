<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Nomenclador\Agencia;

class AgenciaControllerTest extends AdminWebTestCase
{
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
