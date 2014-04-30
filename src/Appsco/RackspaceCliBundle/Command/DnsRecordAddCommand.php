<?php

namespace Appsco\RackspaceCliBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DnsRecordAddCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('appsco:rackspace:dns:record:add')
            ->addArgument('domain', InputArgument::REQUIRED, 'Domain name')
            ->addArgument('name', InputArgument::REQUIRED, 'Record type')
            ->addArgument('type', InputArgument::REQUIRED, 'Record type')
            ->addArgument('data', InputArgument::REQUIRED)
            ->addArgument('priority', InputArgument::OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dns = $this->getDnsService($this->getRackspaceInfo($input));

        $domainName = $input->getArgument('domain');
        $domainList = $dns->domainList(array('name'=>$domainName));

        /** @var \OpenCloud\DNS\Resource\Domain $domain */
        $domain = null;
        foreach ($domainList as $d) {
            if ($d->name == $domainName) {
                $domain = $d;
                break;
            }
        }

        if (!$domain) {
            throw new \InvalidArgumentException('Domain does not exist');
        }

        $recordData = array(
            'ttl' => 3600,
            'name' => $input->getArgument('name'),
            'type' => $input->getArgument('type'),
            'data' => $input->getArgument('data')
        );
        $priority = $input->getArgument('priority');
        if ($priority) {
            $recordData['priority'] = $priority;
        }

        $record = $domain->record();
        $asyncResponse = $record->create($recordData);
        $asyncResponse->waitFor('COMPLETED', 300, $this->getAsyncResponseWait(), 1);
        $output->writeln("");
    }
} 