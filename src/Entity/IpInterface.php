<?php

namespace BCode\IpPinger\Entity;

interface IpInterface
{
	
	/**
     * @return string
     */
    public function getId(): string;

    /**
     * @param string $id
     * @return IpInterface
     */
    public function setId(string $id): IpInterface;
	
    /**
     * @return string
     */
    public function getIp(): string;

    /**
     * @param string $ip
     * @return IpInterface
     */
    public function setIp(string $ip): IpInterface;

}
