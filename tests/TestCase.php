<?php

namespace Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use LBausch\CephRadosgwAdmin\Config;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Get a config instance with mocked handlers.
     *
     * @param array<int, mixed> $container
     * @param array<int, mixed> $responses
     */
    protected function getConfigWithMockedHandlers(array &$container, array $responses = []): Config
    {
        return Config::make([
            'httpClientHandlerStack' => $this->getHandlerStack($container),
            'httpClientHandler' => $this->getMockHandler($responses),
        ]);
    }

    /**
     * Get a handler stack with history middleware.
     *
     * @param array<int, mixed> $container
     *
     * @see https://docs.guzzlephp.org/en/stable/testing.html#history-middleware
     */
    protected function getHandlerStack(array &$container): HandlerStack
    {
        $history = Middleware::history($container); // @phpstan-ignore-line

        $handlerStack = HandlerStack::create();

        $handlerStack->push($history);

        return $handlerStack;
    }

    /**
     * Get a mock handler.
     *
     * @param array<int, mixed> $responses
     *
     * @see https://docs.guzzlephp.org/en/stable/testing.html#mock-handler
     */
    protected function getMockHandler(array $responses = []): MockHandler
    {
        return new MockHandler($responses);
    }
}
