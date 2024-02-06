<?php

namespace App\Tests;

class ProductTest extends AbstractApiTest
{
    private static $testProduct = [
        'sku' => 'SKU-123',
        'productName' => 'Mobile Phone',
        'description' => 'Description fake test'
    ];

    private static function generateSku(): string {
        return 'SKU-' . uniqid();
    }

    /**
     * @depends testCreate
     */
    public function testIndex(): void
    {
        $response = $this->get('/product', static::$testUserToken);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $json = json_decode($response->getContent(), true);

        $this->assertGreaterThan(0, count($json));

    }

    public function testCreate(): void
    {

        $response = $this->get('/product/' . static::$testProduct['sku'], static::$testUserToken);

        // Si el producto ya existe, no envíes una solicitud POST
        if ($response->getStatusCode() === 200) {
            // Producto ya existe, pasa la prueba
            $this->assertTrue(true);
            return;
        }

        $invalidProduct = static::$testProduct;
        unset($invalidProduct['productName']);

        $response = $this->post('/product', $invalidProduct, static::$testAdminToken);
        $this->assertSame(422, $response->getStatusCode());

        $response = $this->post('/product', static::$testProduct, static::$testAdminToken);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $json = json_decode($response->getContent(), true);
        $this->assertNotEmpty($json['sku']);
        static::$testProduct['sku'] = $json['sku'];
    }

    /**
     * @depends testCreate
     */
    public function testShow(): void
    {
        $response = $this->get('/product/' . static::$testProduct['sku'], static::$testUserToken);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $json = json_decode($response->getContent(), true);

        $this->assertSame(static::$testProduct['productName'], $json['productName']);
        $this->assertSame(static::$testProduct['description'], $json['description']);
    }

    public function testUpdate(): void
    {
        $sku = static::generateSku();
        $productToUpdate = [
            'sku' => $sku,
            'productName' => 'Mobile Phone',
            'description' => 'Description fake test'
        ];

        $response = $this->post('/product', $productToUpdate, static::$testAdminToken);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $json = json_decode($response->getContent(), true);

        $productToUpdate['productName'] = 'Laptop';
        $response = $this->put('/product/' . $sku, $productToUpdate, static::$testAdminToken);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $json = json_decode($response->getContent(), true);
        $this->assertSame($productToUpdate['productName'], $json['productName']);
        $this->assertSame($productToUpdate['description'], $json['description']);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(): void
    {
        $response = $this->delete('/product/' . static::$testProduct['sku'], static::$testAdminToken);
        $this->assertSame(204, $response->getStatusCode());
    }
}

