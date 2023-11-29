<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiControllerTest extends WebTestCase
{
    public function testExchangeValuesSuccess()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/exchange/values',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"first": 1, "second": 2}'
        );

        $response = $client->getResponse();

        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString('{"message":"Values exchanged and saved"}', $response->getContent());
    }

    public function testExchangeValuesValidationFailed()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/exchange/values',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"first": "invalid", "second": 2}'
        );

        $response = $client->getResponse();
        
        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testExchangeValuesTypeError()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/exchange/values',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"first": "invalid", "second": "also_invalid"}'
        );

        $response = $client->getResponse();

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testGetHistory()
    {
        $client = static::createClient();

        $client->request('GET', '/api/get/history');

        $response = $client->getResponse();
        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    public function testGetHistoryWithPagination()
    {
        $client = static::createClient();

        $client->request('GET', '/api/get/history-with-pagination', ['page' => '1', 'limit'=> '10', 'sortBy'=> 'id', 'sortOrder'=> 'asc']);
        $request = $client->getRequest();

        // Output information about the request for debugging
        $response = $client->getResponse();
        $this->assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        
        // Decode the JSON response
        $responseData = json_decode($response->getContent(), true);
        
        // Assert the expected structure of the JSON response
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('page', $responseData);
        $this->assertArrayHasKey('totalItems', $responseData);
        $this->assertArrayHasKey('totalPages', $responseData);
    }

    public function testGetHistoryWithPaginationInvalidPage()
    {
        $client = static::createClient();

        $client->request('GET', '/api/get/history-with-pagination', ['page' => 'invalid', 'limit'=> '10']);

        $response = $client->getResponse();

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
       
        $this->assertSame(array(0=>"Invalid page number."), $responseData['error']);
    }

    public function testGetHistoryWithPaginationInvalidLimit()
    {
        $client = static::createClient();

        $client->request('GET', '/api/get/history-with-pagination', ['page' => '1', 'limit'=> 'invalid']);

        $response = $client->getResponse();

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertSame(array(0=>"Invalid limit."), $responseData['error']);
    }

    public function testGetHistoryWithPaginationInvalidSortBy()
    {
        $client = static::createClient();

        $client->request('GET', '/api/get/history-with-pagination', ['page' => '1', 'limit'=> '10', 'sortBy' => 'invalid']);

        $response = $client->getResponse();

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertSame(array(0=>"Invalid sort field."), $responseData['error']);
    }

    public function testGetHistoryWithPaginationInvalidSortOrder()
    {
        $client = static::createClient();

        $client->request('GET', '/api/get/history-with-pagination', ['page' => '1', 'limit'=> '10', 'sortBy' => 'id', 'sortOrder' => 'invalid']);

        $response = $client->getResponse();

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertSame(array(0=>"Invalid sort order."), $responseData['error']);
    }
}
