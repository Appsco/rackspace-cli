#!/usr/bin/env php
<?php

require_once __DIR__ . '/../autoload.php';

$app = new \Symfony\Component\Console\Application('appsco-rackspace-cli', '1.0');
$input = new \Symfony\Component\Console\Input\ArgvInput();

$rackspaceInfoProvider = new \Appsco\RackspaceCli\Service\RackspaceInfoProvider();

$finder = new \Symfony\Component\Finder\Finder();
$finder->in(__DIR__.'/../src/Appsco/RackspaceCli/Command');
/** @var \SplFileInfo $file */
foreach ($finder as $file) {
    $name = $file->getBasename() ."\n";
    $extension = $file->getExtension();
    $class = substr($name, 0, strlen($name) - strlen($extension)-2);
    if ($class == 'AbstractCommand') {
        continue;
    }
    $class = "\\Appsco\\RackspaceCli\\Command\\{$class}";
    /** @var \Appsco\RackspaceCli\Command\AbstractCommand $cmd */
    $cmd = new $class();
    if (false == $cmd instanceof \Appsco\RackspaceCli\Command\AbstractCommand) {
        continue;
    }
    $cmd->setRackspaceInfoProvider($rackspaceInfoProvider);
    $app->add($cmd);
}

$app->run($input);
