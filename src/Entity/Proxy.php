<?php

namespace Retrowaver\ProxyChecker\Entity;

class Proxy implements ProxyInterface
{
	
	/**
     * @var string id
     */
    protected $id;
	
    /**
     * @var string IP or host
     */
    protected $ip;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string|null
     */
    protected $username;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var string protocol (http / https / socks4 / socks4a / socks5 / socks5h)
     */
    protected $protocol;

    public function __toString()
    {
        if (strlen($this->username) && strlen($this->password)) {
            return sprintf(
                "%s://%s:%s@%s:%d",
                $this->protocol,
                $this->username,
                $this->password,
                $this->ip,
                $this->port
            );
        }

        return sprintf(
            "%s://%s:%d",
            $this->protocol,
            $this->ip,
            $this->port
        );
    }
	
	public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): ProxyInterface
    {
        $this->id = $id;
        return $this;
    }
	
    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): ProxyInterface
    {
        $this->ip = $ip;
        return $this;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): ProxyInterface
    {
        $this->port = $port;
        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): ProxyInterface
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): ProxyInterface
    {
        $this->password = $password;
        return $this;
    }

    public function getProtocol(): string
    {
        return $this->protocol;
    }

    public function setProtocol(string $protocol): ProxyInterface
    {
        $this->protocol = strtolower($protocol);
        return $this;
    }
}
