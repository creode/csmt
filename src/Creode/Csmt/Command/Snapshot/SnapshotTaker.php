<?php

namespace Creode\Csmt\Command\Snapshot;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class SnapshotTaker extends Snapshot
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        if ($this->isTestEnvironment()) {
            $this->sendErrorResponse('Snapshot Details command cannot be run on test environments');
        }

        $this->takeSnapshot();
    }

    /**
     * Takes a snapshot
     */
    abstract public function takeSnapshot();
}
