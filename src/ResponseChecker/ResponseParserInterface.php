<?php

namespace Retrowaver\ProxyChecker\ResponseChecker;

use Psr\Http\Message\ResponseInterface;
use Retrowaver\ProxyChecker\Entity\ProxyInterface;

interface ResponseParserInterface
{
    /**
     * Evaluates `$response` obtained using `$proxy`
     * 
     * Returned value is used to decide if proxy is working or not.
     * 
     * @param ResponseInterface $response
     * @param ProxyInterface $proxy
     * @return bool
     */
    public function parseResponse(
        ResponseInterface $response,
        ProxyInterface $proxy
    ): ResponseParserInterface;
}
