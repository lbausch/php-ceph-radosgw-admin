<?php

namespace LBausch\PhpRadosgwAdmin\Signature;

use LBausch\PhpRadosgwAdmin\Contracts\SignatureContract;

abstract class AbstractSignature implements SignatureContract
{
    /**
     * Name of the request signature option.
     *
     * @var string
     */
    public const SIGNATURE_OPTION = 'aws-signature';
}
