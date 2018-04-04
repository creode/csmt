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

    /**
     * Downloads a snapshot
     */
    abstract public function downloadSnapshot($duration);
}
