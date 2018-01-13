<?php

namespace Creode\Csmt\Command\Snapshot\Filesystem;

use Creode\Csmt\Command\Snapshot\SnapshotPull;

class SnapshotPullCommand extends SnapshotPull
{
    protected function configure()
    {
        $this->setName('snapshot:filesystem:pull');
        $this->setDescription('Pull latest filesystem snapshot');
    }

    public function pullSnapshot()
    {
        $filesystem = $this->_config->get('filesystem');

        if (count($filesystem)) {
            foreach($filesystem as $label => $details) {
                $outfile = $this->getLocalStorageDir() . $details['filename'];

                $this->_storage->pull($details['destination'], $outfile, $details['storage']);
            }
        }

        $this->sendSuccessResponse('Pulled filesystem snapshots from remote storage');
    }
}
