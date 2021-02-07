<?php

namespace Retrowaver\ProxyChecker;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Retrowaver\ProxyChecker\ResponseChecker\ResponseCheckerInterface;
use Retrowaver\ProxyChecker\ResponseChecker\ResponseParserInterface;
use Retrowaver\ProxyChecker\Entity\ProxyInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Promise\PromiseInterface;

class ProxyPinger
{
    /**
     * @var ClientInterface
     */
    protected $client;
	
	/**
     * @var array
     */
    protected $ips;

    /**
     * @param RequestInterface $request
     * @param ResponseCheckerInterface $responseChecker
     * @param array|null $options
     * @param array|null $requestOptions
     * @param ClientInterface|null $guzzle
     */
    public function __construct(
       
    ) {

        $this->client = $guzzle ?? new Client(['timeout'=>1]);
	$this->ips = [];
    }

    /**
     * @param ProxyInterface[] $proxies
     * @return ProxyInterface[]
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
     * @param ProxyInterface[] $proxies
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
            'timeout' => 50
        ];
    }
}
