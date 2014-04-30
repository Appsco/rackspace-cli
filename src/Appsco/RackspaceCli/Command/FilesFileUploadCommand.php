<?php

namespace Appsco\RackspaceCli\Command;

use OpenCloud\ObjectStore\Upload\ConsecutiveTransfer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FilesFileUploadCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('appsco:rackspace:files:file:upload')
            ->addArgument('container', InputArgument::REQUIRED, 'Container name to upload file to')
            ->addArgument('filename', InputArgument::REQUIRED, 'Filename to upload')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name of the uploaded file, defaults to local name')
            ->addOption('part-size', 'z', InputOption::VALUE_OPTIONAL, 'Part size')
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

        $filename = $input->getArgument('filename');
        if (!is_file($filename)) {
            throw new \InvalidArgumentException(sprintf("Specified file '%s' does not exist", $filename));
        }

        $name = $input->getArgument('name');
        if (!$name) {
            $pathInfo = pathinfo($filename);
            $name = $pathInfo['basename'];
        }

        $partSize = $input->getOption('part-size');
        if (!$partSize) {
            $fileSize = filesize($filename);
            if ($fileSize > 4900000000) {
                $partSize = 4900000000;
            }
        }

        if (!$partSize) {
            list($response) = $container->uploadObjects(array(array(
                'name' => $name,
                'path' => $filename,
            )));
        } else {
            /** @var ConsecutiveTransfer $transfer */
            $transfer = $container->setupObjectTransfer(array(
                'name' => $name,
                'path' => $filename,
                'partSize' => $partSize
            ));
            $response = $transfer->upload();
        }

        $output->writeln($response->getRawHeaders());
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