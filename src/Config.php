<?php

namespace LBausch\CephRadosgwAdmin;

use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use LBausch\CephRadosgwAdmin\Signature\SignatureV2;
use LBausch\CephRadosgwAdmin\Signature\SignatureV4;

class Config
{
    /**
     * Configuration.
     */
    protected array $config = [];

    protected function __construct(array $config = [])
    {
        $defaults = $this->defaults();

        foreach ($defaults as $key => $value) {
            $this->config[$key] = $config[$key] ?? $value;
        }
    }

    /**
     * Factory method.
     */
    public static function make(array $config = []): self
    {
        return new self($config);
    }

    /**
     * Get option.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Set option.
     *
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        $this->config[$key] = $value;
    }

    /**
     * Get default configuration.
     */
    protected function defaults(): array
    {
        return [
            /*
             * AWS service
             *
             * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html#service
             */
            'service' => 's3',

            /*
             * AWS region
             *
             * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html#cfg-region
             */
            'region' => 'us-east-1',

            /*
             * AWS version
             *
             * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html#cfg-version
             */
            'version' => '2006-03-01',

            /*
             * Admin path for Gateway REST API
             */
            'adminPath' => 'admin/',

            /*
             * Credentials
             *
             * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html#credentials
             */
            'credentials' => null,

            /*
             * V2 signature
             */
            'signatureV2' => SignatureV2::class,

            /*
             * V4 signature
             */
            'signatureV4' => SignatureV4::class,

            /*
             * HTTP client
             *
             * https://docs.guzzlephp.org/en/stable/index.html
             */
            'httpClient' => null,

            /*
             * HTTP client config
             *
             * https://docs.guzzlephp.org/en/stable/request-options.html
             */
            'httpClientConfig' => [],

            /*
             * HTTP client handler stack
             *
             * https://docs.guzzlephp.org/en/stable/handlers-and-middleware.html#handlerstack
             */
            'httpClientHandlerStack' => new HandlerStack(),

            /*
             * HTTP client handler
             *
             * https://docs.guzzlephp.org/en/stable/handlers-and-middleware.html#handlers
             */
            'httpClientHandler' => new CurlHandler(),

            /*
             * HTTP client default headers
             *
             * https://docs.guzzlephp.org/en/stable/request-options.html#headers
             */
            'httpClientHeaders' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => Client::class.':'.Client::VERSION,
            ],
        ];
    }
}
