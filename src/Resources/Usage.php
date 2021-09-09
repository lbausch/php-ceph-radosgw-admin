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
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#get-usage
     */
    public function info(array $data = []): ApiResponse
    {
        return $this->api->get($this->endpoint, [
            RequestOptions::QUERY => $data,
        ]);
    }

    /**
     * Trim usage info.
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#trim-usage
     */
    public function trim(array $data = []): ApiResponse
    {
        return $this->api->delete($this->endpoint, [
            RequestOptions::QUERY => $data,
        ]);
    }
}
