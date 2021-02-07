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

class ProxyChecker
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseCheckerInterface
     */
    protected $responseChecker;
	
	/**
     * @var ResponseParserInterface
     */
    protected $responseParser;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $requestOptions;

    /**
     * @var ClientInterface
     */
    protected $client;
	
	/**
     * @var array
     */
    protected $proxies;

    /**
     * @param RequestInterface $request
     * @param ResponseCheckerInterface $responseChecker
     * @param array|null $options
     * @param array|null $requestOptions
     * @param ClientInterface|null $guzzle
     */
    public function __construct(
        RequestInterface $request,
        ResponseCheckerInterface $responseChecker,
        ResponseParserInterface $responseParser,
        ?array $options = [],
        ?array $requestOptions = [],
        ?ClientInterface $guzzle = null
    ) {
        $this->request = $request;
        $this->responseChecker = $responseChecker;
		$this->responseParser = $responseParser;
        $this->options = $options + $this->getDefaultOptions();
        $this->requestOptions = $requestOptions + $this->getDefaultRequestOptions();
        $this->client = $guzzle ?? new Client();
		$this->proxies = [];
    }

    /**
     * @param ProxyInterface[] $proxies
     * @return ProxyInterface[]
     */
    public function checkProxies(array $proxies): array
    {
        $proxyIndexMap = array_keys($proxies);
		
		$requestOptions = $this->requestOptions;
		
		$this->startTime = microtime(true);

        $eachPromise = new EachPromise($this->getPromiseGenerator($proxies)(), [
            'concurrency' => $this->options['concurrency'],
            'fulfilled' => function (ResponseInterface $response, int $index) use ($proxies, &$validProxies, $proxyIndexMap): void {
               
			    $proxy = $proxies[$proxyIndexMap[$index]];
			
				$endTime = microtime(true);
				$executionTime = round($endTime - $this->startTime, 2);
			
                if ($this->responseChecker->checkResponse($response, $proxy)) {
                	
					$parsedResponse = $this->responseParser->parseResponse($response, $proxy);
					
                    $this->proxies[] = ["status" => true, "executionTime" => $executionTime, "responseBody" => $response->getBody(), "country" => $parsedResponse->country, "proxy_detected" => $parsedResponse->proxy_detected, "proxy" => $proxy];
                }
				else{
					$this->proxies[] = ["status" => false, "executionTime" => $executionTime, "responseBody" => $response->getBody(), "proxy" => $proxy];			
				}
            },
            'rejected' => function (\Exception $reason, int $index) use ($proxies, $requestOptions, $proxyIndexMap): void {
            	
				$proxy = $proxies[$proxyIndexMap[$index]];
				
                $this->proxies[] = ["status" => false, "executionTime" => $requestOptions['timeout'], "responseBody" => $reason->getMessage(), "proxy" => $proxy];	
            }
        ]);

        $eachPromise->promise()->wait();
        return $this->proxies;
    }

    /**
     * @param ProxyInterface[] $proxies
     * @return \Closure
     */
    protected function getPromiseGenerator(array $proxies): \Closure
    {
        return function () use ($proxies): \Generator {
            foreach ($proxies as $proxy) {
            	
                yield $this->client->sendAsync(
                    $this->request,
                    [
                        'proxy' => (string)$proxy,
                        'verify'  => false,
                        'debug' => true,
                        'http_errors' => false,
                        'allow_redirects' => true,
                        'curl' => [
				            //CURLOPT_SSLVERSION => 3
				            CURLOPT_SSL_VERIFYHOST => false,
				            CURLOPT_SSL_VERIFYPEER => false
				        ],
                    ] + $this->requestOptions
                );
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
