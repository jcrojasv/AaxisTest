<?php

namespace App\Tests;

class ProductTest extends AbstractApiTest
{
    private static $testProduct = [
        'id' => 1,
        'sku' => 'ABC-123',
        'productName' => 'Mobile Phone',
        'description' => 'Description fake test'
    ];

    /**
     * @depends testCreate
     */
    public function testIndex(): void
    {
        $response = $this->get('/test', static::$testUserToken);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $json = json_decode($response->getContent(), true);
        $this->assertTrue(in_array(static::$testProduct, $json));
    }

    public function testCreate(): void
    {
        $invalidProduct = static::$testProduct;
        unset($invalidProduct['productName']);

        $response = $this->post('/product', $invalidProduct, static::$testAdminToken);
        $this->assertSame(422, $response->getStatusCode());

        $response = $this->post('/product', static::$testProduct, static::$testAdminToken);
        var_dump($response);
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
        $this->assertEquals(static::$testProduct, $json);
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(): void
    {
        static::$testProduct['productName'] = 'Laptop';
        $response = $this->put('/product/' . static::$testProduct['sku'], static::$testProduct, static::$testAdminToken);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(static::$testProduct, $json);
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

