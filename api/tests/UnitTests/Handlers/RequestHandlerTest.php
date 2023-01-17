<?php

namespace UnitTests\Handlers;

use App\Handlers\RequestHandler;
use ReflectionException;
use UnitTests\BaseTestCase;

class RequestHandlerTest extends BaseTestCase
{
    private RequestHandler $requestHandler;

    protected function setUp(): void
    {
        $this->requestHandler = new RequestHandler();
    }

    /**
     * @dataProvider extractRequestParametersDataProvider
     *
     * @param string $uri
     * @param string|null $requestBody
     * @param array $expectedResults
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function testExtractRequestParameters(string $uri, ?string $requestBody, array $expectedResults): void
    {
        $results = $this->invokeMethod($this->requestHandler, 'extractRequestParameters', [$uri, $requestBody]);
        $this->assertSame($expectedResults, $results);
    }

    /**
     * @return array
     */
    public function extractRequestParametersDataProvider(): array
    {
        return [
            [
                'uri' => '/shout/test-name?limit=2',
                'requestBody' => '',
                'expectedResults' => [
                    'pathVariables' => ['', 'shout', 'test-name'],
                    'queryParams' => ['limit' => '2'],
                ],
            ],
            [
                'uri' => '/shout/test-name?limit=2',
                'requestBody' => '{"test":"value"}',
                'expectedResults' => [
                    'pathVariables' => ['', 'shout', 'test-name'],
                    'queryParams' => ['limit' => '2'],
                    'body' => ['test' => 'value'],
                ],
            ]
        ];
    }
}