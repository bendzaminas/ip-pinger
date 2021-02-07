<?php

namespace BCode\IpPinger;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use BCode\IpPinger\Entity\IpInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Promise\PromiseInterface;

class IpPinger
{
    /**
     * @var ClientInterface
     */
    protected $client;
	
	/**
     * @var array
     */
    protected $options;
	
	/**
     * @var array
     */
    protected $requestOptions;
	
	/**
     * @var array
     */
    protected $ips;

    /**
     * @param array|null $options
     * @param array|null $requestOptions
     * @param ClientInterface|null $guzzle
     */
    public function __construct(
        ?array $options = [],
        ?array $requestOptions = [],
        ?ClientInterface $guzzle = null
    ) {
	$this->options = $options + $this->getDefaultOptions();
        $this->requestOptions = $requestOptions + $this->getDefaultRequestOptions();
        $this->client = $guzzle ?? new Client($this->requestOptions);
	$this->ips = [];
    }

    /**
     * @param IpInterface[] $ips
     * @return IpInterface[]
     */
    public function pingIps(array $ips): array
    {
        $proxyIndexMap = array_keys($ips);
		

        $eachPromise = new EachPromise($this->getPromiseGenerator($ips)(), [
            'concurrency' => $this->options['concurrency'],
            'fulfilled' => function (ResponseInterface $response, int $index) use ($ips, &$validIps, $ipIndexMap): void {
               
		$ip = $ips[$ipIndexMap[$index]];
					
                $this->ips[] = ["status" => true, "ip" => $ip];
        
            },
            'rejected' => function (\Exception $reason, int $index) use ($ips, $requestOptions, $ipIndexMap): void {
            	
		$ip = $ips[$ipIndexMap[$index]];
					
                $this->ips[] = ["status" => false, "ip" => $ip];	
		    
            }
        ]);

        $eachPromise->promise()->wait();
        return $this->ips;
    }

    /**
     * @param IpsInterface[] $ips
     * @return \Closure
     */
    protected function getPromiseGenerator(array $ips): \Closure
    {
        return function () use ($ips): \Generator {
            foreach ($ips as $ip) {
                yield $this->client->headAsync((string)$ip);
            }
        };
    }

    /**
     * @return array
     */
    protected function getDefaultOptions(): array
    {
        return [
            'concurrency' => 50
        ];
    }

    /**
     * @return array
     */
    protected function getDefaultRequestOptions(): array
    {
        return [
            'timeout' => 1
        ];
    }
}
