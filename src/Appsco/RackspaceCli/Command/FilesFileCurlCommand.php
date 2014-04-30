<?php

namespace Appsco\RackspaceCli\Command;

use OpenCloud\ObjectStore\Upload\ConsecutiveTransfer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class FilesFileCurlCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('appsco:rackspace:files:file:curl')
            ->addArgument('container', InputArgument::REQUIRED, 'Container name')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of file in container to download')
            ->addArgument('filename', InputArgument::OPTIONAL, 'Filename to save downloaded file to, defaults to STDOUT')
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

        $name = $input->getArgument('name');
        /** @var \Guzzle\Http\Url $url */
        $url = $container->getUrl($name);
        $token = $container->getClient()->getToken();

        $output->writeln($token);
        $output->writeln((string)$url);
        $output->writeln('');

        $cmd = sprintf("curl -H \"X-Auth-Token: %s\"  %s", $token, (string)$url);

        $filename = $input->getArgument('filename');
        if ($filename) {
            $cmd .= ' > '.$filename;
        }

        $output->writeln($cmd);
        $output->writeln('');

        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        print $process->getOutput();
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