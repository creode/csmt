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

    /**
     * returns storage details by name
     * @param string $identifier 
     * @return array
     * @throws \Exception
     */
    protected function getStorageDetails($identifier) {
        $storage = $this->_config->get('storage');

        if (isset($storage[$identifier])) {
            return $storage[$identifier];
        }

        throw new \Exception('Storage identifier ' . $identifier . ' was not found');
    }


    /**
     * Pushes a local file to the remote storage
     * @param string $localFile 
     * @param string $destinationDirectory 
     * @param string $destinationFilename 
     * @param string $storageIdentifier 
     * @return void
     */
    protected function pushToStorage($localFile, $destinationDirectory, $destinationFilename, $storageIdentifier) {
        if (filesize($localFile) == 0) {
            throw new \Exception('Local file (' . $localFile . ') size is zero');
        }

        $storageDetails = $this->getStorageDetails($storageIdentifier);

        $this->_storage->push(
            $localFile,
            $destinationDirectory . DIRECTORY_SEPARATOR . $destinationFilename,
            $storageDetails
        );

        // delete the local file
        unlink($localFile);
    }
}
