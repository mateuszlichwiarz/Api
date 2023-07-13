<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ProductsTest extends ApiTestCase
{
    //use RefreshDatabaseTrait;

    private const API_TOKEN = '30f31604f100de1c143fd840fdce95bc4976b0400429b48a788e42871434de498aadf3c85365bc01c8a1f8a7a15050b659c02f4e5fc5a983c0c30fa3';

    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame(
            'content-type', 'application/ld+json; charset=utf-8'
        );

        $this->assertJsonContains([
            '@context'         => '/api/contexts/Product',
            '@id'              => '/api/products',
            '@type'            => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view'       => [
                '@id'         => '/api/products?page=1',
                '@type'       => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/products?page=1',
                'hydra:last'  => '/api/products?page=20',
                'hydra:next'  => '/api/products?page=2',
            ],
        ]);
        
        $this->assertCount(5, $response->toArray()['hydra:member']);
    }

    public function testPagination(): void
    {
        $response = static::createClient()->request('GET', '/api/products?page=2');

        $this->assertJsonContains([
            'hydra:view'       => [
                '@id'           => '/api/products?page=2',
                '@type'         => 'hydra:PartialCollectionView',
                'hydra:first'   => '/api/products?page=1',
                'hydra:last'    => '/api/products?page=20',
                'hydra:previous' => '/api/products?page=1',
                'hydra:next'    => '/api/products?page=3',
            ],
        ]);
    }

    /*
    public function testCreateProduct(): void
    {
        static::createClient()->request('POST', '/api/products', [
            'json' => [
                'mpn' => '5794390407',
                'name' => 'A Test Product',
                'description' => 'A Test Description.',
                'issueDate' => '1985-07-31T00:00:00+00:00',
                'manufacturer' => '/api/manufacturers/1',
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->assertResponseHeaderSame(
            'content-type', 'application/ld+json; charset=utf-8'
        );

        $this->assertJsonContains([
            'mpn'          => '5794390407',
            'name'         => 'A Test Product',
            'description'  => 'A Test Description',
            'issueDate'    => '2023-07-05']);

    }
    */

    public function testUpdateProduct(): void
    {
        $client = static::createClient();

        $client->request('PUT', '/api/products/2', ['json' => [
            'description' => 'An updated description',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id'        => '/api/products/2',
            'description' => 'An updated description',
        ]);
    }

}