<?php

namespace App\Tests\Controller\Admin;

use DOMElement;

class EstructuraControllerTest extends AdminWebTestCase
{
    public function testLista(): void
    {
        $crawler = $this->client->request('GET', '/admin/estructura/');
        $this->assertResponseIsSuccessful();
        $this->assertEquals(3, $crawler->filter('li[class="dd-item"]')->count());
//        $this->assertSelectorExists('select[name="filterField"]');
//        $this->assertSelectorExists('input[name="filterValue"]');
//        $this->assertSelectorTextContains('button[class="btn btn-sm btn-success btn-block"]', 'Filtrar');
        $this->assertSelectorTextContains('div[class="panel-heading hbuilt"]', 'Listado de las estructuras');
    }

    public function testCrearMinisterio(): void
    {
        $crawler = $this->client->request('GET', '/admin/estructura/new');
        $buttonCrawlerNode = $crawler->selectButton('Guardar');
        $form = $buttonCrawlerNode->form();

        $tipo = $crawler->filter('#estructura_tipos > option')->eq(1)->attr('value');
        $grupo = $crawler->filter('#estructura_grupos > option')->first()->attr('value');
        $municipio = $crawler->filter('#estructura_municipio > optgroup[label="La Habana"] > option')->first()->attr('value');

        # set values on a form object
        $form['estructura[codigo]'] = 'ESTRUCTURA_1';
        $form['estructura[nombre]'] = 'Estructura 1';
        $form['estructura[descripcion]'] = 'Estructura 1';
        $form['estructura[codigo_postal]'] = '00000';
        $form['estructura[tipos]'] = [$tipo];
        $form['estructura[grupos]'] = [$grupo];
        $form['estructura[municipio]'] = $municipio;
        $form['estructura[habilitado]'] = true;

        # submit the Form object
        $this->client->submit($form);

        $this->assertEquals(303, $this->client->getResponse()->getStatusCode());
    }


    public function testCreaOsde(): void
    {
        $crawler = $this->client->request('GET', '/admin/estructura/new');
        $buttonCrawlerNode = $crawler->selectButton('Guardar');
        $form = $buttonCrawlerNode->form();

        $pertenece = null;
        /** @var DOMElement $domElement */
        foreach ($crawler->filter('#estructura_parent')->children() as $domElement) {
            if ($domElement->textContent === 'Estructura 1')
                $pertenece = $domElement->getAttribute('value');
        }

        if (!$pertenece)
            throw new \InvalidArgumentException('Error no se encontró la "Estructura 1"');

        $tipo = null;
        /** @var DOMElement $domElement */
        foreach ($crawler->filter('#estructura_tipos')->children() as $domElement) {
            if ($domElement->textContent === 'OSDE')
                $tipo = $domElement->getAttribute('value');
        }

        if (!$tipo)
            throw new \InvalidArgumentException('Error no se encontró el tipo "OSDE"');

        $grupo = $crawler->filter('#estructura_grupos > option')->first()->attr('value');
        $municipio = $crawler->filter('#estructura_municipio > optgroup > option')->first()->attr('value');

        # set values on a form object
        $form['estructura[parent]'] = $pertenece;
        $form['estructura[codigo]'] = 'ESTRUCTURA_2';
        $form['estructura[nombre]'] = 'Estructura 2';
        $form['estructura[descripcion]'] = 'Estructura 2';
        $form['estructura[codigo_postal]'] = '00000';
        $form['estructura[tipos]'] = [$tipo];
        $form['estructura[grupos]'] = [$grupo];
        $form['estructura[municipio]'] = $municipio;
        $form['estructura[habilitado]'] = true;

        # submit the Form object
        $this->client->submit($form);

        $this->assertEquals(303, $this->client->getResponse()->getStatusCode());
    }
}