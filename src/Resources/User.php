<?php

namespace LBausch\PhpRadosgwAdmin\Resources;

use LBausch\PhpRadosgwAdmin\ApiResponse;
use LBausch\PhpRadosgwAdmin\Signature\AbstractSignature;
use LBausch\PhpRadosgwAdmin\Signature\SignatureV2;

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
            'query' => [
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
            'query' => array_merge([
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
            'query' => array_merge([
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
            'query' => [
                'uid' => $uid,
                'purge-data' => $purgeData,
            ],
        ]);
    }
}
