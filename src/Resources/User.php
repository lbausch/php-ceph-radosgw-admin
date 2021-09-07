<?php

namespace LBausch\CephRadosgwAdmin\Resources;

use GuzzleHttp\RequestOptions;
use LBausch\CephRadosgwAdmin\ApiResponse;
use LBausch\CephRadosgwAdmin\Signature\AbstractSignature;
use LBausch\CephRadosgwAdmin\Signature\SignatureV2;

class User extends AbstractResource
{
    /**
     * Endpoint to use.
     */
    protected string $endpoint = 'user';

    /**
     * Get a list of all users.
     */
    public function list(): ApiResponse
    {
        return $this->api->get('metadata/'.$this->endpoint);
    }

    /**
     * Get user info.
     */
    public function info(string $uid): ApiResponse
    {
        return $this->api->get($this->endpoint, [
            RequestOptions::QUERY => [
                'uid' => $uid,
            ],
        ]);
    }

    /**
     * Create user.
     */
    public function create(string $uid, $displayName, array $data = []): ApiResponse
    {
        return $this->api->put($this->endpoint, [
            RequestOptions::QUERY => array_merge([
                'uid' => $uid,
                'display-name' => $displayName,
            ], $data),
            AbstractSignature::SIGNATURE_OPTION => SignatureV2::class,
        ]);
    }

    /**
     * Modify user.
     */
    public function modify(string $uid, array $data = []): ApiResponse
    {
        return $this->api->post($this->endpoint, [
            RequestOptions::QUERY => array_merge([
                'uid' => $uid,
            ], $data),
        ]);
    }

    /**
     * Delete user.
     */
    public function delete(string $uid, bool $purgeData = false): ApiResponse
    {
        return $this->api->delete($this->endpoint, [
            RequestOptions::QUERY => [
                'uid' => $uid,
                'purge-data' => $purgeData,
            ],
        ]);
    }
}
