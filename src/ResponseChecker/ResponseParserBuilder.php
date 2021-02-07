<?php

namespace Retrowaver\ProxyChecker\ResponseChecker;

use Psr\Http\Message\ResponseInterface;
use Retrowaver\ProxyChecker\Entity\ProxyInterface;

class ResponseParserBuilder implements ResponseParserInterface
{
    
    public $country = '';
	public $proxy_detected = true;

    public function parseResponse(
        ResponseInterface $response,
        ProxyInterface $proxy
    ): ResponseParserBuilder {
        	
		$parsed = json_decode($response->getBody());
		
		$this->country = $parsed->country;
		$this->proxy_detected = $parsed->proxy_detected;
		
		
		return $this;
		
    }

}
