<?php

namespace Appsco\RackspaceCliBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class DnsDigCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('appsco:rackspace:dns:dig')
            ->addArgument('domain', InputArgument::REQUIRED, 'Domain name')
            ->addArgument('type', InputArgument::REQUIRED, 'Record type')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Limit')
            ->addOption('offset', 'o', InputOption::VALUE_OPTIONAL, 'Offset')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $this->getQuery($input);
        $recordType = $this->getRecordType($input);

        $dns = $this->getDnsService($this->getRackspaceInfo($input));

        $domainList = $dns->domainList($query);

        $output->writeln(sprintf("Found %s domains:", $domainList->count()));

        /** @var \OpenCloud\DNS\Resource\Domain $domain */
        /** @var \OpenCloud\DNS\Resource\Record $record */

        foreach ($domainList as $domain) {

            $output->writeln('');
            $output->writeln(sprintf("%s\t%s", $domain->id, $domain->name));

            $recordList = $domain->recordList();

            foreach ($recordList as $record) {
                if (!$recordType || in_array($recordType, array('?', '*', '.')) || $recordType == $record->type) {
                    $output->writeln(sprintf("%-18s%-45s%-10s%-10s   %s%s",
                        $record->id,
                        $record->name,
                        $record->type,
                        $record->ttl,
                        $record->priority ? $record->priority.' ' : '',
                        $record->data
                    ));
                }
            }
        }
    }


    /**
     * @param InputInterface $input
     * @return array
     */
    protected function getQuery(InputInterface $input)
    {
        $query = array();

        $limit = $input->getOption('limit');
        if ($limit && $limit < 100) {
            $query['limit'] = $limit;
        }

        $offset = $input->getOption('offset');
        if ($offset) {
            $query['offset'] = $offset;
        }

        $domainName = $input->getArgument('domain');
        if ($domainName && !in_array($domainName, array('?', '*', '.'))) {
            $query['name'] = $domainName;
        }

        return $query;
    }


    /**
     * @param InputInterface $input
     * @return string
     */
    protected function getRecordType(InputInterface $input)
    {
        $recordType = $input->getArgument('type');
        if ($recordType == 'any' || $recordType == '*') {
            $recordType = null;
        }

        return $recordType;
    }

//    protected function getRackspaceInfo(InputInterface $input)
//    {
//        $result = parent::getRackspaceInfo($input);
//
//        $result->setUsePublicUrl(true);
//
//        return $result;
//    }


} 