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
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#get-user-info
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
     *
     * @param array<mixed, mixed> $data
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#create-user
     */
    public function create(string $uid, string $displayName, array $data = []): ApiResponse
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
     *
     * @param array<mixed, mixed> $data
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#modify-user
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
     * Remove user.
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#remove-user
     */
    public function remove(string $uid, bool $purgeData = false): ApiResponse
    {
        return $this->api->delete($this->endpoint, [
            RequestOptions::QUERY => [
                'uid' => $uid,
                'purge-data' => $purgeData,
            ],
        ]);
    }

    /**
     * Create key.
     *
     * @param array<mixed, mixed> $data
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#create-key
     */
    public function createKey(string $uid, array $data = []): ApiResponse
    {
        return $this->api->put($this->endpoint, [
            RequestOptions::QUERY => array_merge([
                'key' => '',
                'uid' => $uid,
            ], $data),
        ]);
    }

    /**
     * Remove key.
     *
     * @param array<mixed, mixed> $data
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#remove-key
     */
    public function removeKey(string $accessKey, array $data = []): ApiResponse
    {
        return $this->api->delete($this->endpoint, [
            RequestOptions::QUERY => array_merge([
                'key' => '',
                'access-key' => $accessKey,
            ], $data),
        ]);
    }

    /**
     * Create subuser.
     *
     * @param array<mixed, mixed> $data
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#create-subuser
     */
    public function createSubuser(string $uid, string $subuser, array $data = []): ApiResponse
    {
        return $this->api->put($this->endpoint, [
            RequestOptions::QUERY => array_merge([
                'uid' => $uid,
                'subuser' => $subuser,
            ], $data),
        ]);
    }

    /**
     * Modify subuser.
     *
     * @param array<mixed, mixed> $data
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#modify-subuser
     */
    public function modifySubuser(string $uid, string $subuser, array $data = []): ApiResponse
    {
        return $this->api->post($this->endpoint, [
            RequestOptions::QUERY => array_merge([
                'uid' => $uid,
                'subuser' => $subuser,
            ], $data),
        ]);
    }

    /**
     * Remove subuser.
     *
     * @param array<mixed, mixed> $data
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#remove-subuser
     */
    public function removeSubuser(string $uid, string $subuser, array $data = []): ApiResponse
    {
        return $this->api->delete($this->endpoint, [
            RequestOptions::QUERY => array_merge([
                'uid' => $uid,
                'subuser' => $subuser,
            ], $data),
        ]);
    }

    /**
     * Add capability.
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#add-a-user-capability
     */
    public function addCapability(string $uid, string $userCaps): ApiResponse
    {
        return $this->api->put($this->endpoint, [
            RequestOptions::QUERY => [
                'caps' => '',
                'uid' => $uid,
                'user-caps' => $userCaps,
            ],
        ]);
    }

    /**
     * Remove capability.
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#remove-a-user-capability
     */
    public function removeCapability(string $uid, string $userCaps): ApiResponse
    {
        return $this->api->delete($this->endpoint, [
            RequestOptions::QUERY => [
                'caps' => '',
                'uid' => $uid,
                'user-caps' => $userCaps,
            ],
        ]);
    }

    /**
     * Get user quota.
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#get-user-quota
     */
    public function getQuota(string $uid): ApiResponse
    {
        return $this->api->get($this->endpoint, [
            RequestOptions::QUERY => [
                'quota' => '',
                'uid' => $uid,
                'quota-type' => 'user',
            ],
        ]);
    }

    /**
     * Set user quota.
     *
     * @param array<mixed, mixed> $quota
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#set-user-quota
     */
    public function setQuota(string $uid, array $quota): ApiResponse
    {
        return $this->api->put($this->endpoint, [
            RequestOptions::QUERY => [
                'quota' => '',
                'uid' => $uid,
                'quota-type' => 'user',
            ],
            RequestOptions::BODY => json_encode($quota),
            AbstractSignature::SIGNATURE_OPTION => SignatureV2::class,
        ]);
    }

    /**
     * Get bucket quota.
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#get-bucket-quota
     */
    public function getBucketQuota(string $uid): ApiResponse
    {
        return $this->api->get($this->endpoint, [
            RequestOptions::QUERY => [
                'quota' => '',
                'uid' => $uid,
                'quota-type' => 'bucket',
            ],
        ]);
    }

    /**
     * Set bucket quota.
     *
     * @param array<mixed, mixed> $quota
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#set-bucket-quota
     */
    public function setBucketQuota(string $uid, array $quota): ApiResponse
    {
        return $this->api->put($this->endpoint, [
            RequestOptions::QUERY => [
                'quota' => '',
                'uid' => $uid,
                'quota-type' => 'bucket',
            ],
            RequestOptions::BODY => json_encode($quota),
            AbstractSignature::SIGNATURE_OPTION => SignatureV2::class,
        ]);
    }
}
