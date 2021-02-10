# PHP Ip pinger
Ip pinger is a PHP library that allows you to quickly async check a list of ips.
- fast (thanks to asynchronous requests)
- simple (PSR-7 based)

## Installation
```
composer require bcode/ip-pinger
```

## 1. Basic usage

### Step 1. Make ip array
Make ip array manually:
```php
use BCode\IpPinger\Entity\Ip;

$ips = [
    (new Ip)
        ->setId('1')
        ->setIp('127.0.0.1'),
    (new Ip)
        ->setId('2')
        ->setIp('127.0.0.1')
];
```

### Step 2. Create IpPinger and check ips
```php
use BCode\IpPinger\IpPinger;

$ipPinger = new IpPinger();

$pingedIps = $ipPinger->pingIps($ips);

foreach($pingedIps as $pingedIp){
    if($pingedIp->getLatency())
    {
         //good $pingedIp->getLatency() prints response in ms      
    }
    else
    {
         //bad $pingedIp->getLatency() prints false      
    }
}
```

## 2. Additional info
### Options reference
`IpPinger` accepts optional parameters `$options` and `$requestOptions`:
- `$options`
    - `concurrency` - max concurrent request (default 50)
- `$requestOptions` are [Guzzle request options](http://docs.guzzlephp.org/en/stable/request-options.html) that are passed to Guzzle client while sending a request. Currently there's only one default value: `'timeout' => 1`
