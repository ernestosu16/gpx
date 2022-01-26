<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Nomenclador\EstructuraTipo;

class EstructuraTipoControllerTest extends AdminWebTestCase
{
    public function testLista(): void
    {
        $crawler = $this->client->request('GET', '/admin/tipo/estructura/');
        $this->assertResponseIsSuccessful();
        $this->assertEquals(8, $crawler->filter('table[class="table table-hover table-bordered table-striped"] tr')->count());
        $this->assertSelectorTextContains('div', 'Sistema de Gestión Paquetería Express Bienvenido al Sistema de Gestión Paquetería Express');
    }

    public function testCrearTipoMipymes(){
        $crawler = $this->client->request('GET', '/admin/tipo/estructura/new');
        $buttonCrawlerNode = $crawler->selectButton('Guardar');
        $form = $buttonCrawlerNode->form();

        $em = $this->getContainer()->get('doctrine')->getManager();
        /** @var EstructuraTipo $tipoEmpresa */
        $tipoEmpresa = $em->getRepository(EstructuraTipo::class)->findOneByCodigo('EMPRESA');


        # set values on a form object
        $form['estructura_tipo[parent]'] = $tipoEmpresa->getId();
        $form['estructura_tipo[codigo]'] = 'MIPYMES';
        $form['estructura_tipo[nombre]'] = 'Mipymes';
        $form['estructura_tipo[descripcion]'] = 'Mipymes';
        $form['estructura_tipo[habilitado]'] = true;

        # submit the Form object
        $this->client->submit($form);

        $this->assertEquals(303, $this->client->getResponse()->getStatusCode());

        /** @var EstructuraTipo $tipoMipymes */
        $tipoMipymes = $em->getRepository(EstructuraTipo::class)->findOneByCodigo('MIPYMES');

        $this->assertEquals($tipoEmpresa->getCodigo(),$tipoMipymes->getParent()->getCodigo());
    }

    public function testBorrarTipoMypimes(){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $tipoMipymes = $em->getRepository(EstructuraTipo::class)->findOneByCodigo('MIPYMES');
        $crawler = $this->client->request('GET', '/admin/tipo/estructura/' . $tipoMipymes->getId() . '/edit');
        $buttonCrawlerNode = $crawler->selectButton('Eliminar');
        $form = $buttonCrawlerNode->form();

        # submit the Form object
        $this->client->submit($form);

        $this->assertEquals(303, $this->client->getResponse()->getStatusCode());
    }
}
