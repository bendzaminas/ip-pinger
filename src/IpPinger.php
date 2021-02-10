<?php

namespace BCode\IpPinger;

use Spatie\Async\Pool;
use JJG\Ping;

class IpPinger
{
    protected $pool;

    public function __construct(
    ) {
	$this->pool = Pool::create();
    }

    public function pingIps(array $ips): array
    {	    
	$pingedIps = [];
	    
    	foreach ($ips as $ip) {

        	$this->pool->add(function () use ($ip) {
			
                       $ping = new Ping($ip->getIp(), 1, 1);
			
                       $latency = $ping->ping('fsockopen');
			
                       $ip->setLatency($latency);
			
		       return $ip;
			
                })->then(function ($ip) use (&$pingedIps) {
                      $pingedIps[] = $ip;
                })->catch(function (Throwable $exception) use (&$pingedIps)  {
		      //todo
                });
         }

         $pool->wait();
	    
	 return $pingedIps;
    }
}
