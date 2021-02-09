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
	
     /**
     * @var string IP
     */
    protected $port;

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
	
    public function setPort(int $port): IpInterface
    {
        $this->port = $port;
        return $this;
    }
	
    public function getIp(): int
    {
        return $this->ip;
    }

    public function setIp(string $ip): IpInterface
    {
        $this->ip = $ip;
        return $this;
    }
	
}
