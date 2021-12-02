<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Estructura;
use App\Entity\Nomenclador\Grupo;
use App\Entity\Trabajador;

class TrabajadorControllerTest extends AdminWebTestCase
{
    public function testLista(): void
    {
        $crawler = $this->client->request('GET', '/admin/trabajador/');
        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, $crawler->filter('div[class="panel-heading hbuilt"]')->count());
        $this->assertSelectorTextContains('div', 'Sistema de Gestión Paquetería Express Bienvenido al Sistema de Gestión Paquetería Express');
    }

    public function testCrearConCredenciales(): void
    {

        $em = $this->getContainer()->get('doctrine')->getManager();
        $osde = $em->getRepository(Estructura::class)->findOneByCodigo('GECC');
        $grupo = $em->getRepository(Grupo::class)->findOneByCodigo('GRUPO_ADMINISTRADOR');

        $crawler = $this->client->request('GET', '/admin/trabajador/new');
        $buttonCrawlerNode = $crawler->selectButton('Guardar');
        $form = $buttonCrawlerNode->form();

        # set values on a form object
        $form['trabajador[credencial][usuario]'] = 'trabajador1';
        $form['trabajador[credencial][contrasena][first]'] = 'abc123';
        $form['trabajador[credencial][contrasena][second]'] = 'abc123';
        $form['trabajador[persona][numero_identidad]'] = '88010111111';
        $form['trabajador[persona][nombre_primero]'] = 'Trabajador';
        $form['trabajador[persona][apellido_primero]'] = 'con';
        $form['trabajador[persona][apellido_segundo]'] = 'Credencial';
        $form['trabajador[cargo]'] = 'Test1';
        $form['trabajador[habilitado]'] = true;
        $form['trabajador[estructura]'] = $osde->getId();
        $form['trabajador[grupos]'] = [$grupo->getId()];

        # submit the Form object
        $this->client->submit($form);

        $this->assertEquals(303, $this->client->getResponse()->getStatusCode());
    }

    public function testEliminarConCredencial(): void
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $trabajador = $em->getRepository(Trabajador::class)->findOneByNumeroIdentidad('88010111111');
        $crawler = $this->client->request('GET', '/admin/trabajador/' . $trabajador->getId() . '/edit');
        $buttonCrawlerNode = $crawler->selectButton('Eliminar');
        $form = $buttonCrawlerNode->form();

        # submit the Form object
        $this->client->submit($form);

        $this->assertEquals(303, $this->client->getResponse()->getStatusCode());
    }

    public function testCrearSinCredenciales(): void
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $osde = $em->getRepository(Estructura::class)->findOneByCodigo('GECC');
        $grupo = $em->getRepository(Grupo::class)->findOneByCodigo('GRUPO_ADMINISTRADOR');

        $crawler = $this->client->request('GET', '/admin/trabajador/new');
        $buttonCrawlerNode = $crawler->selectButton('Guardar');
        $form = $buttonCrawlerNode->form();

        # set values on a form object
        $form['trabajador[persona][numero_identidad]'] = '88010111112';
        $form['trabajador[persona][nombre_primero]'] = 'Trabajador';
        $form['trabajador[persona][apellido_primero]'] = 'con';
        $form['trabajador[persona][apellido_segundo]'] = 'Credencial';
        $form['trabajador[cargo]'] = 'Test1';
        $form['trabajador[habilitado]'] = true;
        $form['trabajador[estructura]'] = $osde->getId();
        $form['trabajador[grupos]'] = [$grupo->getId()];

        # submit the Form object
        $this->client->submit($form);

        $this->assertEquals(303, $this->client->getResponse()->getStatusCode());
    }

    public function testEditarSinCredencial(): void
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $trabajador = $em->getRepository(Trabajador::class)->findOneByNumeroIdentidad('88010111112');
        $crawler = $this->client->request('GET', '/admin/trabajador/' . $trabajador->getId() . '/edit');
        $buttonCrawlerNode = $crawler->selectButton('Guardar');
        $form = $buttonCrawlerNode->form();

        $form['trabajador[persona][apellido_primero]'] = 'sin';

        # submit the Form object
        $this->client->submit($form);

        $this->assertEquals(303, $this->client->getResponse()->getStatusCode());
    }

    public function testEliminarSinCredencial(): void
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $trabajador = $em->getRepository(Trabajador::class)->findOneByNumeroIdentidad('88010111112');
        $crawler = $this->client->request('GET', '/admin/trabajador/' . $trabajador->getId() . '/edit');
        $buttonCrawlerNode = $crawler->selectButton('Eliminar');
        $form = $buttonCrawlerNode->form();

        # submit the Form object
        $this->client->submit($form);

        $this->assertEquals(303, $this->client->getResponse()->getStatusCode());
    }
}
