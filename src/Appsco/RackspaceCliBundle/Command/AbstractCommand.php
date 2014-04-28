<?php

namespace Appsco\RackspaceCliBundle\Command;

use Appsco\RackspaceCliBundle\Model\RackspaceInfo;
use Appsco\RackspaceCliBundle\Service\RackspaceInfoProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use OpenCloud\Rackspace;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;


abstract class AbstractCommand extends ContainerAwareCommand
{
    /** @var  Rackspace */
    private $client;


    protected function configure()
    {
        $this
            ->addOption('username', 'u', InputOption::VALUE_OPTIONAL, 'Rackspace username')
            ->addOption('api-key', 'a', InputOption::VALUE_OPTIONAL, 'Rackspace api key')
            ->addOption('region', 'r', InputOption::VALUE_OPTIONAL, 'Rackspace region')
            ->addOption('public', 'p', InputOption::VALUE_NONE, 'Use public url')
        ;
    }


    /**
     * @param InputInterface $input
     * @return RackspaceInfo
     */
    protected function getRackspaceInfo(InputInterface $input)
    {
        $provider = $this->getRackspaceInfoProvider();

        return $provider->get($input->getOptions());
    }


    /**
     * @return RackspaceInfoProvider
     */
    protected function getRackspaceInfoProvider()
    {
        $provider = $this->getContainer()->get('appsco_rackspace_cli.rackspace_info_provider');

        return $provider;
    }


    /**
     * @param RackspaceInfo $info
     * @return Rackspace
     */
    protected function getRackspaceClient(RackspaceInfo $info)
    {
        if (!$this->client) {
            $this->client = new Rackspace(Rackspace::UK_IDENTITY_ENDPOINT, array(
                'username' => $info->getUsername(),
                'apiKey' => $info->getApiKey()
            ));
        }

        return $this->client;
    }


    /**
     * @param RackspaceInfo $info
     * @return \OpenCloud\DNS\Service
     */
    protected function getDnsService(RackspaceInfo $info)
    {
        $client = $this->getRackspaceClient($info);

        return $client->dnsService(null, $info->getRegion(), $info->getUsePublicUrl() ? 'publicURL' : 'internalURL');
    }

    /**
     * @param RackspaceInfo $info
     * @return \OpenCloud\ObjectStore\Service
     */
    protected function getFilesService(RackspaceInfo $info)
    {
        $client = $this->getRackspaceClient($info);

        return $client->objectStoreService(null, $info->getRegion(), $info->getUsePublicUrl() ? 'publicURL' : 'internalURL');
    }


    protected function getAsyncResponseWait()
    {
        return function($object) {
            if (!empty($object->error)) {
                var_dump($object->error); die;
            }
            print sprintf("%s/%-12s %4s%%\r",
                $object->name(),
                $object->status(),
                isset($object->progress) ? $object->progress : 0
            );
        };
    }
} 