<?php

namespace LBausch\CephRadosgwAdmin;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\HandlerStack;
use InvalidArgumentException;
use LBausch\CephRadosgwAdmin\Middlewares\SignatureMiddleware;
use LBausch\CephRadosgwAdmin\Resources\Bucket;
use LBausch\CephRadosgwAdmin\Resources\Usage;
use LBausch\CephRadosgwAdmin\Resources\User;
use ReflectionClass;

/**
 * @method Bucket bucket()
 * @method Usage  usage()
 * @method User   user()
 */
class Client
{
    /**
     * Version.
     *
     * @var string
     */
    public const VERSION = '0.2.0';

    /**
     * Configuration.
     */
    protected Config $config;

    /**
     * HTTP client.
     */
    protected HttpClientInterface $httpClient;

    protected function __construct(string $base_uri, string $key, string $secret, Config $config = null)
    {
        // Setup configuration
        $this->config = (null === $config) ? Config::make() : $config;

        // Set base_uri
        $this->config->set('base_uri', $base_uri);

        // Set credentials
        $this->config->set('credentials', $this->config->get('credentials', new Credentials($key, $secret)));

        // Create handler stack for HTTP client
        /** @var HandlerStack $stack */
        $stack = $this->config->get('httpClientHandlerStack');

        // Add handler
        $stack->setHandler($this->config->get('httpClientHandler'));

        // Add middleware which signs requests
        $stack->push(SignatureMiddleware::handle($this->config));

        // Setup HTTP client
        $this->httpClient = $this->config->get(
            'httpClient', new HttpClient(
                array_merge(
                    [
                        'handler' => $stack,
                        'base_uri' => $base_uri.'/'.ltrim($this->config->get('adminPath'), '/'),
                        'headers' => $this->config->get('httpClientHeaders'),
                    ],
                    $this->config->get('httpClientConfig', [])
                )
            )
        );
    }

    /**
     * Factory method.
     */
    public static function make(string $base_uri, string $key, string $secret, Config $config = null): self
    {
        return new self($base_uri, $key, $secret, $config);
    }

    /**
     * Get HTTP client.
     */
    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    /**
     * Get S3 client.
     */
    public function getS3Client(string $key = null, string $secret = null, array $options = []): S3Client
    {
        $credentials = $this->config->get('credentials');

        if (null !== $key && null !== $secret) {
            $credentials = new Credentials($key, $secret);
        }

        $options = array_merge([
            'endpoint' => $this->config->get('base_uri'),
            'credentials' => $credentials,
            'version' => $this->config->get('version'),
            'region' => $this->config->get('region'),
        ], $options);

        return new S3Client($options);
    }

    /**
     * Call resources via magic method.
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments = [])
    {
        $reflection = new ReflectionClass($this);

        $resource = $reflection->getNamespaceName().'\\Resources\\'.ucfirst($name);

        if (!class_exists($resource)) {
            throw new InvalidArgumentException('Invalid resource '.$resource);
        }

        return call_user_func([$resource, 'withClient'], $this); // @phpstan-ignore-line
    }
}
