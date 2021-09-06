<?php

namespace LBausch\CephRadosgwAdmin\Signature;

use LBausch\CephRadosgwAdmin\Contracts\SignatureContract;

abstract class AbstractSignature implements SignatureContract
{
    /**
     * Name of the request signature option.
     *
     * @var string
     */
    public const SIGNATURE_OPTION = 'aws-signature';
}
