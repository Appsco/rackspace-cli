<?php

namespace Appsco\RackspaceCliBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class FilesContainerListCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('appsco:rackspace:files:container:list')
            ->addArgument('filter', InputArgument::OPTIONAL, 'Prefix')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Limit')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->getFilesService($this->getRackspaceInfo($input));

        $containerList = $service->listContainers($this->getQuery($input));

        /** @var \OpenCloud\ObjectStore\Resource\Container $container */

        foreach ($containerList as $container)
        {
            $output->writeln(sprintf('%s', $container->name));
        }
    }


    /**
     * @param InputInterface $input
     * @return array
     */
    protected function getQuery(InputInterface $input)
    {
        $query = array();

        $filter = $input->getArgument('filter');
        if ($filter) {
            $query['prefix'] = $filter;
        }

        $limit = $input->getOption('limit');
        if ($limit && $limit < 100) {
            $query['limit'] = $limit;
        }

        return $query;
    }
} 