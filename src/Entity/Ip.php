<?php

namespace BCode\IpPinger\Entity;

class Ip implements IpInterface
{
	
	/**
     * @var string id
     */
    protected $id;
	
    /**
     * @var string IP
     */
    protected $ip;
    
    protected $latency;

    public function __toString()
    {
        return $this->ip . ':' . $this->port;
    }
	
	public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): IpInterface
    {
        $this->id = $id;
        return $this;
    }
	
    public function getLatency()
    {
        return $this->id;
    }

    public function setLatency($latency): IpInterface
    {
        $this->latency = $latency;
        return $this;
    }
	
    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): IpInterface
    {
        $this->ip = $ip;
        return $this;
    }
	
}
