<?php

namespace LBausch\CephRadosgwAdmin\Resources;

use LBausch\CephRadosgwAdmin\ApiResponse;

class Bucket extends AbstractResource
{
    /**
     * Endpoint to use.
     */
    protected string $endpoint = 'bucket';

    /**
     * Get a list of all buckets.
     */
    public function list(): ApiResponse
    {
        return $this->api->get('metadata/'.$this->endpoint);
    }

    /**
     * Get bucket info.
     */
    public function info(array $data): ApiResponse
    {
        return $this->api->get($this->endpoint, [
            'query' => $data,
        ]);
    }

    /**
     * Remove bucket.
     */
    public function delete(string $bucket, array $data = []): ApiResponse
    {
        return $this->api->delete($this->endpoint, [
            'query' => array_merge(['bucket' => $bucket], $data),
        ]);
    }

    /**
     * Check bucket index.
     */
    public function check(string $bucket, array $data = []): ApiResponse
    {
        return $this->api->get($this->endpoint, [
            'query' => array_merge([
                'index' => '',
                'bucket' => $bucket,
            ], $data),
        ]);
    }

    /**
     * Link bucket.
     */
    public function link(string $bucket, string $uid, array $data = []): ApiResponse
    {
        return $this->api->put($this->endpoint, [
            'query' => array_merge([
                'bucket' => $bucket,
                'uid' => $uid,
            ], $data),
        ]);
    }

    /**
     * Unlink bucket.
     */
    public function unlink(string $bucket, string $uid): ApiResponse
    {
        return $this->api->post($this->endpoint, [
            'query' => [
                'bucket' => $bucket,
                'uid' => $uid,
            ],
        ]);
    }

    /**
     * Read the policy of an object or bucket.
     */
    public function policy(string $bucket, array $data = []): ApiResponse
    {
        return $this->api->get($this->endpoint, [
            'query' => array_merge([
                'policy' => '',
                'bucket' => $bucket,
            ], $data),
        ]);
    }
}
