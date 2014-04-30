<?php

namespace Appsco\RackspaceCli\Service;

use Appsco\RackspaceCli\Model\RackspaceInfo;

class RackspaceInfoProvider
{
    public function get(array $options)
    {
        $result = new RackspaceInfo(
            $this->getUsername($options),
            $this->getApiKey($options),
            $this->getRegion($options),
            $this->getUrlType($options)
        );

        return $result;
    }


    protected function getUsername(array $options)
    {
        $result = @$options['username'] ?: getenv('RAX_USERNAME');
        if (!$result) {
            throw new \RuntimeException('Username not specified nor environment variable RAX_USERNAME should be set with your username');
        }
        return $result;
    }

    protected function getApiKey(array $options)
    {
        $result = @$options['api-key'] ?: getenv('RAX_API_KEY');
        if (!$result) {
            throw new \RuntimeException('Api key not specified nor environment variable RAX_API_KEY should be set with your api key');
        }
        return $result;
    }

    protected function getRegion(array $options)
    {
        $result = $options['region'] ?: getenv('RAX_REGION');
        if (!$result) {
            throw new \RuntimeException('Region not specified nor environment variable RAX_REGION should be set with your username');
        }
        return $result;
    }

    protected function getUrlType(array $options)
    {
        $result = $options['public'] ?: getenv('RAX_PUBLIC');
        return (bool)$result;
    }
} 