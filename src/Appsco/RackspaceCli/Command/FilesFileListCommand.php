<?php

namespace Appsco\RackspaceCli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FilesFileListCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('appsco:rackspace:files:file:list')
            ->addArgument('container', InputArgument::REQUIRED)
            ->addArgument('filter', InputArgument::OPTIONAL, 'Prefix')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Limit')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->getFilesService($this->getRackspaceInfo($input));

        $containerName = $input->getArgument('container');

        /** @var \OpenCloud\Common\Collection $containerList */
        $containerList = $service->listContainers(array('prefix'=>$containerName));

        /** @var \OpenCloud\ObjectStore\Resource\Container $container */
        $container = null;

        foreach ($containerList as $c) {
            if ($c->getName() == $containerName) {
                $container = $c;
                break;
            }
        }

        if (!$container) {
            throw new \InvalidArgumentException(sprintf("Container '%s' not found", $containerName));
        }

        $query = $this->getQuery($input);

        $fileList = $container->objectList($query);

        $output->writeln(sprintf("%s: %s files", $containerName, $fileList->count()));

        /** @var \OpenCloud\ObjectStore\Resource\DataObject $file */

        foreach ($fileList as $file)
        {
            $output->writeln(sprintf('%-15s%-20s%-20s%s',
                $file->getContentLength(),
                $file->getContentType(),
                $file->getContainer()->getName(),
                $file->getName()
            ));
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