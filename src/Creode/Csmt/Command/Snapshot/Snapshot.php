<?php

namespace Creode\Csmt\Command\Snapshot;

use Creode\Csmt\Command\BaseCommand;

abstract class Snapshot extends BaseCommand {

    /**
     * @var \Creode\Csmt\Config\Config $config
     */
    protected $_config;

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

        parent::__construct($responder);
    }

    /**
     * Takes a snapshot
     */
    abstract public function takeSnapshot();
}
