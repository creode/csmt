<?php

namespace Creode\Csmt\Command\Snapshot\Database;

use Creode\Csmt\Command\Snapshot\SnapshotPull;

class SnapshotPullCommand extends SnapshotPull
{
    protected function configure()
    {
        $this->setName('snapshot:database:pull');
        $this->setDescription('Pull latest DB snapshot');
    }

    public function pullSnapshot()
    {
        $databases = $this->_config->get('databases');

        if (count($databases)) {
            foreach($databases as $filename => $databaseDetails) {
                $outfile = $this->getLocalStorageDir() . $databaseDetails['filename'];

                $this->_storage->pull($databaseDetails['destination'], $outfile, $databaseDetails['storage']);
            }
        }

        $this->sendSuccessResponse('Pulled DB snapshots from remote storage');
    }
}
