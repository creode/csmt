<?php

namespace Creode\Csmt\Command\Snapshot;

use Creode\Csmt\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Snapshot extends BaseCommand
{
    /**
     * Constructor
     * @param \Creode\Csmt\Config\Config $config 
     */
    public final function __construct(
        \Creode\Csmt\Config\Config $config,
        \Creode\Csmt\Storage\Storage $storage,
        \Creode\Csmt\Response\Responder $responder
    ) {
        $this->_config = $config;
        $this->_storage = $storage;

        parent::__construct($config, $responder);
    }
}
