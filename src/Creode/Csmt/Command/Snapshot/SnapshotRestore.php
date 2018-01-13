<?php

namespace Creode\Csmt\Command\Snapshot;

use Creode\Csmt\System\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class SnapshotRestore extends Snapshot
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        if ($this->isLiveEnvironment()) {
            $this->sendErrorResponse('Snapshot Restore command cannot be run on live environments');
        }

        $this->restoreSnapshots();
    }

    /**
     * Restores all snapshots
     */
    abstract public function restoreSnapshots();
}
