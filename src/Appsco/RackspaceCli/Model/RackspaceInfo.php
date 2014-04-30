<?php

namespace Appsco\RackspaceCli\Model;

class RackspaceInfo 
{
    /** @var  string */
    protected $username;

    /** @var  string */
    protected $apiKey;

    /** @var  string */
    protected $region;

    /** @var  bool */
    protected $usePublicUrl;


    /**
     * @param $username
     * @param $apiKey
     * @param $region
     * @param $usePublicUrl
     */
    public function __construct($username, $apiKey, $region, $usePublicUrl)
    {
        $this->username = $username;
        $this->apiKey = $apiKey;
        $this->region = $region;
        $this->usePublicUrl = $usePublicUrl;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param boolean $usePublicUrl
     */
    public function setUsePublicUrl($usePublicUrl)
    {
        $this->usePublicUrl = $usePublicUrl;
    }

    /**
     * @return boolean
     */
    public function getUsePublicUrl()
    {
        return $this->usePublicUrl;
    }


} 