<?php

namespace LBausch\CephRadosgwAdmin\Resources;

use GuzzleHttp\RequestOptions;
use LBausch\CephRadosgwAdmin\ApiResponse;
use LBausch\CephRadosgwAdmin\Signature\AbstractSignature;
use LBausch\CephRadosgwAdmin\Signature\SignatureV2;

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
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#get-bucket-info
     */
    public function info(array $data): ApiResponse
    {
        return $this->api->get($this->endpoint, [
            RequestOptions::QUERY => $data,
        ]);
    }

    /**
     * Remove bucket.
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#unlink-bucket
     */
    public function remove(string $bucket, array $data = []): ApiResponse
    {
        return $this->api->delete($this->endpoint, [
            RequestOptions::QUERY => array_merge(['bucket' => $bucket], $data),
        ]);
    }

    /**
     * Check bucket index.
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#check-bucket-index
     */
    public function check(string $bucket, array $data = []): ApiResponse
    {
        return $this->api->get($this->endpoint, [
            RequestOptions::QUERY => array_merge([
                'index' => '',
                'bucket' => $bucket,
            ], $data),
        ]);
    }

    /**
     * Link bucket.
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#link-bucket
     */
    public function link(string $bucket, string $uid, array $data = []): ApiResponse
    {
        return $this->api->put($this->endpoint, [
            RequestOptions::QUERY => array_merge([
                'bucket' => $bucket,
                'uid' => $uid,
            ], $data),
        ]);
    }

    /**
     * Unlink bucket.
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#unlink-bucket
     */
    public function unlink(string $bucket, string $uid): ApiResponse
    {
        return $this->api->post($this->endpoint, [
            RequestOptions::QUERY => [
                'bucket' => $bucket,
                'uid' => $uid,
            ],
        ]);
    }

    /**
     * Read the policy of an object or bucket.
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#get-bucket-or-object-policy
     */
    public function policy(string $bucket, array $data = []): ApiResponse
    {
        return $this->api->get($this->endpoint, [
            RequestOptions::QUERY => array_merge([
                'policy' => '',
                'bucket' => $bucket,
            ], $data),
        ]);
    }

    /**
     * Remove object.
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#remove-object
     */
    public function removeObject(string $bucket, string $object): ApiResponse
    {
        return $this->api->delete($this->endpoint, [
            RequestOptions::QUERY => [
                'bucket' => $bucket,
                'object' => $object,
            ],
        ]);
    }

    /**
     * Set bucket quota.
     *
     * @see https://docs.ceph.com/en/latest/radosgw/adminops/#set-quota-for-an-individual-bucket
     */
    public function setQuota(string $uid, string $bucket, array $quota): ApiResponse
    {
        return $this->api->put($this->endpoint, [
            RequestOptions::QUERY => [
                'quota' => '',
                'uid' => $uid,
                'bucket' => $bucket,
            ],
            RequestOptions::BODY => json_encode($quota),
            AbstractSignature::SIGNATURE_OPTION => SignatureV2::class,
        ]);
    }
}
