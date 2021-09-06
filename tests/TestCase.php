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
     * https://docs.guzzlephp.org/en/stable/testing.html#history-middleware
     */
    protected function getHandlerStack(array &$container): HandlerStack
    {
        $history = Middleware::history($container);

        $handlerStack = HandlerStack::create();

        $handlerStack->push($history);

        return $handlerStack;
    }

    /**
     * Get a mock handler.
     *
     * https://docs.guzzlephp.org/en/stable/testing.html#mock-handler
     */
    protected function getMockHandler(array $responses = []): MockHandler
    {
        return new MockHandler($responses);
    }
}
