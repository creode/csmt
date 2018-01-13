<?php

namespace Creode\Csmt\Command\Snapshot;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class SnapshotPull extends Snapshot
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        if ($this->isLiveEnvironment()) {
            $this->sendErrorResponse('Snapshot Pull command cannot be run on live environments');
        }

        $this->pullSnapshot();
    }

    /**
     * Takes a snapshot
     */
    abstract public function pullSnapshot();
}
