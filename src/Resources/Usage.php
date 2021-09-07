<?php

namespace LBausch\CephRadosgwAdmin\Resources;

use GuzzleHttp\RequestOptions;
use LBausch\CephRadosgwAdmin\ApiResponse;

class Usage extends AbstractResource
{
    /**
     * Endpoint to use.
     */
    protected string $endpoint = 'usage';

    /**
     * Get usage info.
     */
    public function info(array $data = []): ApiResponse
    {
        return $this->api->get($this->endpoint, [
            RequestOptions::QUERY => $data,
        ]);
    }

    /**
     * Trim usage info.
     */
    public function trim(array $data = []): ApiResponse
    {
        return $this->api->delete($this->endpoint, [
            RequestOptions::QUERY => $data,
        ]);
    }
}
