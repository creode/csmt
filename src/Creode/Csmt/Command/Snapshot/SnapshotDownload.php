<?php

namespace Creode\Csmt\Command\Snapshot;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class SnapshotDownload extends Snapshot
{
    protected function configure()
    {
        $this->addOption(
            'duration',
            'd',
            InputOption::VALUE_REQUIRED,
            'Time (in minutes) that link will be valid for',
            2
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        $duration = $input->getOption('duration');

        $this->downloadSnapshot($duration);
    }

    protected function addFileToDownload(array &$downloadLinks, $filename, $duration, array $details)
    {
        // support for old versions of csmt.yml where `destination` was a full file path
        $destination = isset($details['remote_dir'])
                ? $details['remote_dir'] . '/' . $filename
                : $details['destination'];

        $storage = $this->getStorageDetails($details['storage']['general']);

        $downloadLinks[$filename] = $this->_storage->downloadLink($destination, $duration, $storage);
    }

    /**
     * Downloads a snapshot
     */
    abstract public function downloadSnapshot($duration);
}
