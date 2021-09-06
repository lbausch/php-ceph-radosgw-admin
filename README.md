# PHP Ceph Radosgw Admin  <!-- omit in toc -->

![sca](https://github.com/lbausch/php-ceph-radosgw-admin/actions/workflows/sca.yml/badge.svg) ![tests](https://github.com/lbausch/php-ceph-radosgw-admin/actions/workflows/tests.yml/badge.svg) ![codecov](https://codecov.io/gh/lbausch/php-ceph-radosgw-admin/branch/master/graph/badge.svg)

A PHP REST client for the [Ceph](https://ceph.io/) [Object Gateway](https://docs.ceph.com/en/latest/radosgw/) [Admin Ops API](https://docs.ceph.com/en/latest/radosgw/adminops/)

- [Features](#features)
- [Installation](#installation)
- [Setup](#setup)
- [Usage](#usage)

## Features
+ Support all endpoints of the [Admin Ops API](https://docs.ceph.com/en/latest/radosgw/adminops/), including currently undocumented `metadata/` endpoints
+ Requests are signed using AWS Signature V2 and V4
+ Provides S3Client with no extra configuration by utilizing the [AWS SDK for PHP
](https://aws.amazon.com/sdk-for-php/)
+ Extensible and customizable


## Installation
Use [Composer](https://getcomposer.org/) to install the library:
```bash
composer require lbausch/php-radosgw-admin
```

## Setup
An admin user with sufficient capabilities is required:

```bash
radosgw-admin user create --uid="admin" --display-name="Admin User" --caps="users=read,write;usage=read,write;buckets=read,write;metadata=read,write;zone=read,write" --admin --access-key="<redacted>" --secret="<redacted>"
```

## Usage

### Admin Client
```php
use LBausch\PhpRadosgwAdmin\Client;
use LBausch\PhpRadosgwAdmin\Resources\Bucket;

require 'vendor/autoload.php';

$client = Client::make('http://gateway:8080', 'access key', 'secret key');

$response = $client->bucket()->list();

print_r($response->get());

/*
Array
(
    [0] => mybucket
)
*/
```

### Handling Exceptions
Upon failed requests the exception `LBausch\PhpRadosgwAdmin\ApiException` is thrown.

```php
use LBausch\PhpRadosgwAdmin\ApiException;
use LBausch\PhpRadosgwAdmin\Client;

require 'vendor/autoload.php';

$client = Client::make('http://gateway:8080', 'access key', 'secret key');

try {
    $client->user()->delete('non existent user');
} catch (ApiException $exception) {
    // Exception handling
}

```

### S3 Client
```php
use LBausch\PhpRadosgwAdmin\Client;

require 'vendor/autoload.php';

$client = Client::make('http://gateway:8080', 'access key', 'secret key');

$s3client = $client->getS3Client();

// Or use different credentials
// $s3client = $client->getS3Client('different access key', 'different secret key');

$buckets = $s3client->listBuckets();

foreach ($buckets['Buckets'] as $bucket) {
    echo $bucket['Name']."\n";
}

// mybucket
```

### Custom Configuration
Many settings may be overriden if needed. See `LBausch\PhpRadosgwAdmin\Config::defaults()` for a list of configurable options.

```php
use LBausch\PhpRadosgwAdmin\Client;
use LBausch\PhpRadosgwAdmin\Config;

require 'vendor/autoload.php';

$config = Config::make([
    // Set a custom admin path
    'adminPath' => 'mgt/',
]);

// Override settings after instantiation, e.g. specify a timeout for requests
$config->set('httpClientConfig', [
    'timeout' => 10,
]);

$client = Client::make('http://gateway:8080', 'access key', 'secret key', $config);
```
