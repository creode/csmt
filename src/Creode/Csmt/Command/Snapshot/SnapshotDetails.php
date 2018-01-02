<?php

namespace Creode\Csmt\Command\Snapshot;

use Creode\Csmt\Command\BaseCommand;
use Creode\Csmt\System\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class SnapshotDetails extends BaseCommand
{
    /**
     * @var \Creode\Csmt\Config\Config $config
     */
    protected $_config;

    /**
     * @var array
     */
    private $_files = [];

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        $this->getSnapshotInfo();
    }

    /**
     * Adds a file to the response
     * @param File $file 
     * @return type
     */
    protected function addFileToResponse(File $file) 
    {
        $this->_files[] = $file;
    }

    /**
     * Sends the success response with the file details
     */
    protected function snapshotInfoSuccess() 
    {
        $this->sendSuccessResponse(['files' => $this->_files]);
    }

    /**
     * Retrieves info about a snapshot. Should call addFileToResponse()
     * with info about the file retrieved
     */
    abstract public function getSnapshotInfo();
}
